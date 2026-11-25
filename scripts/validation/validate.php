<?php

declare(strict_types=1);

use Opis\JsonSchema\Errors\ErrorFormatter;
use Opis\JsonSchema\Validator;

$root = dirname(__DIR__, 2);
require $root . '/vendor/autoload.php';

$arguments = array_slice($argv, 1);
$graph = in_array('--graph', $arguments, true);
$paths = array_values(array_filter($arguments, static fn (string $argument): bool => $argument !== '--graph'));
if (count($paths) > 1) {
    fwrite(STDERR, "Usage: validate.php [--graph] [catalogue.json]\n");
    exit(2);
}

try {
    $cataloguePath = $paths[0] ?? $root . '/planning/catalog.json';
    $catalogue = json_decode(file_get_contents($cataloguePath), false, 512, JSON_THROW_ON_ERROR);
    $schema = json_decode(file_get_contents($root . '/planning/catalog.schema.json'), false, 512, JSON_THROW_ON_ERROR);
    $result = (new Validator())->validate($catalogue, $schema);
    if (!$result->isValid()) {
        fwrite(STDERR, json_encode((new ErrorFormatter())->format($result->error()), JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR) . "\n");
        exit(1);
    }

    $errors = [];
    $packages = [];
    $namespaces = [];
    $directories = [];
    $unknown = 0;
    foreach ($catalogue->packages as $package) {
        $name = $package->name;
        $slug = substr($name, strlen('paickage/'));
        if (isset($packages[$name])) {
            $errors[] = "Duplicate package: {$name}";
        }
        foreach ($namespaces as $namespace) {
            $candidate = strtolower($package->namespace);
            if ($namespace === $candidate || str_starts_with($namespace, $candidate . '\\') || str_starts_with($candidate, $namespace . '\\')) {
                $errors[] = "Overlapping namespace: {$package->namespace}";
            }
        }
        $namespaces[] = strtolower($package->namespace);
        $packages[$name] = $package;
        foreach (['scope', 'layer', 'dependencies', 'extensions'] as $facet) {
            $finding = $package->architecture->{$facet};
            if ($finding->value === null) {
                $unknown++;
            }
            if ($finding->state === 'approved') {
                $reference = $finding->approval_ref;
                if (!preg_match('~^planning/decisions/[a-zA-Z0-9-]+\.md(?:#[a-zA-Z0-9-]+)?$~', $reference) || !is_file($root . '/' . explode('#', $reference)[0])) {
                    $errors[] = "{$name}: {$facet} approval must reference an existing decision record.";
                }
            }
            if (in_array($finding->state, ['verified', 'approved'], true) && empty($finding->evidence)) {
                $errors[] = "{$name}: {$facet} requires research evidence.";
            }
        }
        foreach ($package->research_refs as $reference) {
            if (!preg_match('~^planning/(research|proposals)/[a-zA-Z0-9-]+\.md$~', $reference) || !is_file($root . '/' . $reference)) {
                $errors[] = "{$name}: missing or invalid research reference {$reference}";
            }
        }
        if ($package->directory === null) {
            if (is_dir($root . '/packages/' . $slug)) {
                $errors[] = "{$name}: production directory exists without catalogue registration.";
            }
            continue;
        }
        $directories[] = $package->directory;
        if ($package->directory !== 'packages/' . $slug) {
            $errors[] = "{$name}: directory does not match package identity.";
        }
        foreach (['scope', 'layer', 'dependencies', 'extensions'] as $facet) {
            if ($package->architecture->{$facet}->state !== 'approved') {
                $errors[] = "{$name}: implemented package requires approved {$facet}.";
            }
        }
        $manifestPath = $root . '/' . $package->directory . '/composer.json';
        if (!is_file($manifestPath)) {
            $errors[] = "{$name}: missing package composer.json.";
            continue;
        }
        $manifest = json_decode(file_get_contents($manifestPath), true, 512, JSON_THROW_ON_ERROR);
        if (($manifest['name'] ?? null) !== $name || ($manifest['autoload']['psr-4'][$package->namespace . '\\'] ?? null) !== 'src/') {
            $errors[] = "{$name}: package identity or package-local PSR-4 mapping differs.";
        }
        if (!isset($manifest['require']['php'])) {
            $errors[] = "{$name}: PHP requirement is missing.";
        }
        foreach (['provide', 'replace', 'repositories'] as $shortcut) {
            if (!empty($manifest[$shortcut])) {
                $errors[] = "{$name}: {$shortcut} requires a separately reviewed validation policy.";
            }
        }
        $actualPackages = [];
        $actualExtensions = [];
        foreach ($manifest['require'] ?? [] as $dependency => $constraint) {
            if (in_array($dependency, ['php', 'php-64bit'], true)) {
                continue;
            }
            if (str_starts_with($dependency, 'ext-')) {
                $actualExtensions[] = $dependency;
            } elseif (str_starts_with($dependency, 'paickage/')) {
                $actualPackages[] = $dependency;
            } else {
                $errors[] = "{$name}: unauthorized runtime requirement {$dependency}.";
            }
        }
        foreach (['dependencies' => $actualPackages, 'extensions' => $actualExtensions] as $facet => $actual) {
            $approved = $package->architecture->{$facet}->value;
            sort($actual);
            if ($approved !== null) {
                sort($approved);
            }
            if ($actual !== $approved) {
                $errors[] = "{$name}: actual {$facet} differ from catalogue architecture.";
            }
        }
    }

    foreach ($packages as $name => $package) {
        foreach ($package->architecture->dependencies->value ?? [] as $dependency) {
            $target = $packages[$dependency] ?? null;
            if ($target === null) {
                $errors[] = "{$name}: unknown package dependency {$dependency}.";
                continue;
            }
            $layer = $package->architecture->layer->value;
            $targetLayer = $target->architecture->layer->value;
            if ($layer === null || $targetLayer === null || $targetLayer >= $layer) {
                $errors[] = "{$name}: dependency {$dependency} is not verified to flow to a lower layer.";
            }
            if ($package->directory !== null) {
                foreach (['scope', 'layer', 'dependencies', 'extensions'] as $facet) {
                    if ($target->architecture->{$facet}->state !== 'approved') {
                        $errors[] = "{$name}: dependency {$dependency} has no approved {$facet}.";
                    }
                }
            }
        }
    }
    $visit = static function (string $name, array $path) use (&$visit, $packages, &$errors): void {
        if (in_array($name, $path, true)) {
            $errors[] = 'Dependency cycle: ' . implode(' -> ', [...$path, $name]);
            return;
        }
        foreach ($packages[$name]->architecture->dependencies->value ?? [] as $dependency) {
            if (isset($packages[$dependency])) {
                $visit($dependency, [...$path, $name]);
            }
        }
    };
    foreach (array_keys($packages) as $name) {
        $visit($name, []);
    }
    foreach (glob($root . '/packages/*/composer.json') as $manifest) {
        $directory = substr(dirname($manifest), strlen($root) + 1);
        if (!in_array($directory, $directories, true)) {
            $errors[] = "Unregistered package directory: {$directory}";
        }
    }
    if ($errors !== []) {
        fwrite(STDERR, implode("\n", array_unique($errors)) . "\n");
        exit(1);
    }
    if ($graph) {
        echo json_encode(['source' => $paths[0] ?? 'planning/catalog.json', 'packages' => array_map(static fn (object $package): array => [
            'name' => $package->name,
            'layer' => $package->architecture->layer,
            'dependencies' => $package->architecture->dependencies,
        ], array_values($packages))], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . "\n";
    } else {
        echo count($packages) . ' catalogue entries valid; ' . count($directories) . " implemented; {$unknown} unknown architecture facets.\n";
        echo "Manifest checks do not establish runtime/source independence or owner approval authenticity.\n";
    }
} catch (Throwable $error) {
    fwrite(STDERR, 'Validation failed: ' . $error->getMessage() . "\n");
    exit(1);
}

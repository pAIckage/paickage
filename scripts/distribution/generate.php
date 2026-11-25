<?php

declare(strict_types=1);

$output = $argv[1] ?? '';
$revision = $argv[2] ?? '';

if (!str_starts_with($output, '/') || !preg_match('/^[a-f0-9]{40}$/D', $revision)) {
    throw new InvalidArgumentException('Usage: php generate.php /absolute/empty/output source-commit-sha');
}

if (!is_dir($output) || array_values(array_diff(scandir($output), ['.', '..'])) !== []) {
    throw new InvalidArgumentException('Output must be an existing empty directory; existing work is never overwritten.');
}

if (!extension_loaded('zip')) {
    throw new RuntimeException('The development-only generator requires ext-zip.');
}

$registry = $output . '/registry';
mkdir($registry, 0777, true);
$name = 'paickage/distribution-fixture';
$versions = ['0.1.0', '0.2.0'];
$packages = [];
$manifest = [
    'source_revision' => $revision,
    'generator_sha256' => hash_file('sha256', __FILE__),
    'verifier_sha256' => hash_file('sha256', __DIR__ . '/verify.php'),
    'fixture_only' => true,
    'archives' => [],
];
$license = <<<'LICENSE'
MIT License

Copyright (c) PAiCKAGE contributors

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
LICENSE;

foreach ($versions as $version) {
    $package = [
        'name' => $name,
        'description' => 'Disposable PAiCKAGE distribution experiment; not a production library.',
        'type' => 'library',
        'license' => 'MIT',
        'require' => ['php' => '~8.4.27 || ~8.5.12'],
        'autoload' => ['psr-4' => ['Paickage\\DistributionFixture\\' => 'src/Probe/']],
        'extra' => ['paickage-distribution-experiment' => true],
    ];
    $source = str_replace('__FIXTURE_VERSION__', $version, <<<'PHP'
<?php

declare(strict_types=1);

namespace Paickage\DistributionFixture;

/**
 * Identify the installed disposable distribution fixture.
 */

final class Probe
{
    /**
     * Return the fixture version carried by this archive.
     */

    public static function version(): string
    {
        return '__FIXTURE_VERSION__';
    }
}
PHP);
    $files = [
        'composer.json' => json_encode($package, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . "\n",
        'src/Probe/Probe.php' => $source . "\n",
        'README.md' => "# Disposable distribution fixture\n\nDevelopment experiment only; not an approved PAiCKAGE package.\n\n`Paickage\\DistributionFixture\\Probe::version()` returns `{$version}`.\n\nRequires PHP ~8.4.27 or ~8.5.12; no runtime Composer dependencies.\n",
        'LICENSE' => $license . "\n",
    ];
    $archive = $registry . '/distribution-fixture-' . $version . '.zip';
    $zip = new ZipArchive();

    if ($zip->open($archive, ZipArchive::CREATE | ZipArchive::EXCL) !== true) {
        throw new RuntimeException('Unable to create fixture ZIP.');
    }

    foreach ($files as $path => $contents) {
        if (!$zip->addFromString($path, $contents) || !$zip->setMtimeName($path, 1704067200)) {
            throw new RuntimeException('Unable to add a fixture file to ZIP.');
        }
    }

    if (!$zip->close() || $zip->open($archive) !== true || $zip->numFiles !== count($files)) {
        throw new RuntimeException('Unable to inspect the completed ZIP.');
    }

    $hashes = [];

    foreach ($files as $path => $contents) {
        if ($zip->getFromName($path) !== $contents) {
            throw new RuntimeException('Archive inspection failed for ' . $path);
        }

        $hashes[$path] = hash('sha256', $contents);
    }

    $zip->close();
    $package['version'] = $version;
    $package['dist'] = [
        'type' => 'zip',
        'url' => 'file://' . $archive,
        'reference' => $revision,
        'shasum' => hash_file('sha1', $archive),
    ];
    $packages[$name][$version] = $package;
    $manifest['archives'][$version] = [
        'file' => basename($archive),
        'sha256' => hash_file('sha256', $archive),
        'files' => $hashes,
    ];

    $consumer = $output . '/consumer-' . $version;
    mkdir($consumer, 0777, true);
    $configuration = [
        'name' => 'paickage/distribution-consumer',
        'description' => 'Disposable clean consumer for archive installation verification.',
        'license' => 'MIT',
        'repositories' => [
            ['type' => 'composer', 'url' => 'file://' . $registry],
            ['packagist.org' => false],
        ],
        'require' => ['php' => '~8.4.27 || ~8.5.12', $name => '^' . $version],
        'config' => ['allow-plugins' => false],
    ];
    $expected = ['name' => $name, 'version' => $version, 'revision' => $revision, 'files' => $hashes];

    foreach (['composer.json' => $configuration, 'expected.json' => $expected] as $path => $data) {
        if (file_put_contents($consumer . '/' . $path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . "\n") === false) {
            throw new RuntimeException('Unable to write consumer configuration.');
        }
    }

    if (!copy(__DIR__ . '/verify.php', $consumer . '/verify.php')) {
        throw new RuntimeException('Unable to copy the independent consumer verification script.');
    }
}

foreach (['packages.json' => ['packages' => $packages], 'manifest.json' => $manifest] as $path => $data) {
    if (file_put_contents($registry . '/' . $path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . "\n") === false) {
        throw new RuntimeException('Unable to write registry metadata.');
    }
}

echo json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . "\n";

<?php

declare(strict_types=1);

$consumer = __DIR__;
$expected = json_decode(file_get_contents($consumer . '/expected.json'), true, flags: JSON_THROW_ON_ERROR);
$installed = json_decode(file_get_contents($consumer . '/vendor/composer/installed.json'), true, flags: JSON_THROW_ON_ERROR);
$lock = json_decode(file_get_contents($consumer . '/composer.lock'), true, flags: JSON_THROW_ON_ERROR);

if (array_column($installed['packages'], 'name') !== [$expected['name']] || $installed['dev'] !== false || $lock['packages-dev'] !== []) {
    throw new RuntimeException('The clean consumer must contain exactly the fixture, without development packages.');
}

$package = $lock['packages'][0];

if ($package['name'] !== $expected['name'] || $package['version'] !== $expected['version'] || $package['dist']['type'] !== 'zip' || $package['dist']['reference'] !== $expected['revision'] || isset($package['source'])) {
    throw new RuntimeException('Package identity/version/provenance must resolve from the archive metadata, without a source checkout.');
}

if ($installed['packages'][0]['version'] !== $expected['version'] || $installed['packages'][0]['installation-source'] !== 'dist') {
    throw new RuntimeException('The installed package must match the resolved archive version.');
}

require $consumer . '/vendor/autoload.php';

$path = Composer\InstalledVersions::getInstallPath($expected['name']);
$actualFiles = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS));

foreach ($iterator as $file) {
    if ($file->isLink() || !$file->isFile()) {
        throw new RuntimeException('Installed fixture may contain only regular files.');
    }

    $relative = substr($file->getPathname(), strlen($path) + 1);
    $actualFiles[$relative] = hash_file('sha256', $file->getPathname());
}

ksort($actualFiles);
ksort($expected['files']);

if ($actualFiles !== $expected['files']) {
    throw new RuntimeException('Installed package contents differ from the independently inspected archive.');
}

if (Paickage\DistributionFixture\Probe::version() !== $expected['version']) {
    throw new RuntimeException('The installed public API returned an unexpected result.');
}

echo json_encode([
    'result' => 'pass',
    'php' => PHP_VERSION,
    'package' => $package['name'],
    'version' => $package['version'],
    'installation_source' => $installed['packages'][0]['installation-source'],
    'public_api' => Paickage\DistributionFixture\Probe::version(),
    'runtime_packages' => array_column($installed['packages'], 'name'),
    'development_packages' => count($lock['packages-dev']),
    'files_verified_sha256' => array_keys($actualFiles),
    'source_revision' => $expected['revision'],
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . "\n";

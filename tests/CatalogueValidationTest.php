<?php

declare(strict_types=1);

beforeEach(function (): void {
    $root = dirname(__DIR__);
    $this->workspace = sys_get_temp_dir() . '/paickage-catalogue-' . bin2hex(random_bytes(8));
    foreach (['scripts/validation', 'planning/decisions', 'vendor'] as $directory) {
        mkdir($this->workspace . '/' . $directory, 0777, true);
    }
    copy($root . '/scripts/validation/validate.php', $this->workspace . '/scripts/validation/validate.php');
    copy($root . '/planning/catalog.schema.json', $this->workspace . '/planning/catalog.schema.json');
    file_put_contents($this->workspace . '/vendor/autoload.php', '<?php require ' . var_export($root . '/vendor/autoload.php', true) . ';');
    file_put_contents($this->workspace . '/planning/decisions/fixture.md', 'Synthetic validation fixture; not owner approval.');
    $packages = [];
    foreach (['bytes', 'bigint', 'encoding'] as $slug) {
        $packages[] = [
            'name' => 'paickage/' . $slug,
            'namespace' => 'Paickage\\' . ucfirst($slug),
            'directory' => null,
            'description' => 'Synthetic validation fixture.',
            'research_refs' => [],
            'tracking_issue' => 'https://github.com/paickage/paickage/issues/1',
            'architecture' => [
                'scope' => ['state' => 'proposed', 'value' => 'Synthetic scope.'],
                'layer' => ['state' => 'proposed', 'value' => 0],
                'dependencies' => ['state' => 'not_researched', 'value' => null],
                'extensions' => ['state' => 'not_researched', 'value' => null],
            ],
        ];
    }
    $this->catalogue = json_decode(json_encode([
        '$schema' => 'catalog.schema.json',
        'schema_version' => 1,
        'architecture_standard' => 'standards/architecture.md#revision-0001',
        'packages' => $packages,
    ], JSON_THROW_ON_ERROR));
    $this->runValidator = function (array $arguments = []): array {
        $path = $this->workspace . '/planning/catalog.json';
        file_put_contents($path, json_encode($this->catalogue, JSON_THROW_ON_ERROR));
        $process = proc_open([PHP_BINARY, $this->workspace . '/scripts/validation/validate.php', ...$arguments, $path], [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
        fclose($pipes[0]);
        $output = stream_get_contents($pipes[1]) . stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        return [proc_close($process), $output];
    };
});

afterEach(function (): void {
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->workspace, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($files as $file) {
        if ($file->isDir()) {
            rmdir($file->getPathname());
        } else {
            unlink($file->getPathname());
        }
    }
    rmdir($this->workspace);
});

it('rejects PHP namespaces that differ only by case', function (): void {
    $this->catalogue->packages[1]->namespace = 'Paickage\\BYTES';
    [$exit, $output] = ($this->runValidator)();
    expect($exit)->toBe(1)->and($output)->toContain('Overlapping namespace');
});

it('requires every architectural facet of an implemented dependency to be approved', function (string $facet): void {
    $target = $this->catalogue->packages[0];
    $consumer = $this->catalogue->packages[2];
    $this->catalogue->packages = [$target, $consumer];
    foreach ([$target, $consumer] as $package) {
        $package->research_refs = [];
        foreach (['scope', 'layer', 'dependencies', 'extensions'] as $finding) {
            $package->architecture->{$finding}->state = 'approved';
            $package->architecture->{$finding}->approval_ref = 'planning/decisions/fixture.md';
            $package->architecture->{$finding}->evidence = ['synthetic fixture only'];
            if (in_array($finding, ['dependencies', 'extensions'], true)) {
                $package->architecture->{$finding}->value = [];
            }
        }
    }
    $consumer->architecture->layer->value = 1;
    $consumer->architecture->dependencies->value = [$target->name];
    $consumer->directory = 'packages/encoding';
    mkdir($this->workspace . '/packages/encoding', 0777, true);
    file_put_contents($this->workspace . '/packages/encoding/composer.json', json_encode([
        'name' => $consumer->name,
        'require' => ['php' => '^8.4', $target->name => '^1.0'],
        'autoload' => ['psr-4' => [$consumer->namespace . '\\' => 'src/']],
    ], JSON_THROW_ON_ERROR));
    [$baseline, $baselineOutput] = ($this->runValidator)();
    expect($baseline)->toBe(0, $baselineOutput);
    $target->architecture->{$facet}->state = 'proposed';
    [$exit, $output] = ($this->runValidator)();
    expect($exit)->toBe(1)->and($output)->toContain('has no approved ' . $facet);
})->with(['scope', 'layer', 'dependencies', 'extensions']);

it('preserves unknown dependencies and the actual graph source', function (): void {
    $this->catalogue->packages = [$this->catalogue->packages[0]];
    $this->catalogue->packages[0]->research_refs = [];
    $this->catalogue->packages[0]->architecture->dependencies = (object) ['state' => 'not_researched', 'value' => null];
    [$exit, $output] = ($this->runValidator)(['--graph']);
    $graph = json_decode($output);
    expect($exit)->toBe(0)
        ->and($graph->source)->toBe($this->workspace . '/planning/catalog.json')
        ->and($graph->packages[0]->dependencies->value)->toBeNull();
    $this->catalogue->packages[0]->architecture->dependencies->value = [];
    [$invalid] = ($this->runValidator)();
    expect($invalid)->toBe(1);
});

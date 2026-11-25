# Local archive distribution experiment

This development-only experiment checks whether independent Composer packages
can be distributed from one monorepo using explicit metadata and package ZIPs.
It does not initialize an approved library or a production registry. Nothing
is published, no release tags are created, and no server is started.

Execute `generate.php`; it is a CLI tool, not a reference implementation for
production packages. It writes a disposable fixture directly into two archives,
inspects every archive member, and creates static Composer metadata and two
consumer projects in an **existing empty output directory**. It refuses to
overwrite existing work. Generated files live outside `packages/` and outside
the repository.

`verify.php` is copied into each consumer so verification does not need the
monorepo. It checks the installed identity/version, ZIP installation source,
exact runtime package set, absence of development packages, installed-file
SHA-256 hashes and the fixture's public API.

## Requirements

Use existing Docker with the official `composer:2` and
`php:8.4.27-cli-alpine` images. The observed Composer image is Composer 2.11.1
on PHP 8.5.12. The generator uses that image's ZIP extension only for
development archive creation. The installed fixture requires no external
runtime Composer packages or additional native extensions. No host PHP,
Composer installation, credentials, published ports or network access is needed
after the images are available.

The commands below use a POSIX shell and Docker bind mounts; this procedure was
verified on Linux. They intentionally use fresh temporary Composer home/cache
directories and disable networking. Run from the monorepo root after committing
the experiment scripts so the supplied source revision identifies their source.

## Repeat the experiment

```sh
experiment_path=$(mktemp -d)
source_revision=$(git rev-parse HEAD)

docker run --rm --network none --user "$(id -u):$(id -g)" \
  -v "$PWD:/source:ro" -v "$experiment_path:/exchange" \
  composer:2 php /source/scripts/distribution/generate.php /exchange "$source_revision"

for fixture_version in 0.1.0 0.2.0; do
  docker run --rm --network none --user "$(id -u):$(id -g)" \
    -e COMPOSER_HOME=/tmp/composer-home -e COMPOSER_CACHE_DIR=/tmp/composer-cache \
    -v "$experiment_path:/exchange" \
    composer:2 validate --working-dir="/exchange/consumer-$fixture_version" \
    --strict --no-check-publish

  docker run --rm --network none --user "$(id -u):$(id -g)" \
    -e COMPOSER_HOME=/tmp/composer-home -e COMPOSER_CACHE_DIR=/tmp/composer-cache \
    -v "$experiment_path:/exchange" \
    composer:2 install --working-dir="/exchange/consumer-$fixture_version" \
    --no-dev --prefer-dist --no-plugins --no-scripts --no-interaction --no-progress

  docker run --rm --network none \
    -v "$experiment_path/consumer-$fixture_version:/consumer:ro" \
    php:8.4.27-cli-alpine php /consumer/verify.php
done
```

Only the generator has the monorepo mounted. The installer receives generated
metadata, ZIPs and consumer files. The final PHP container receives **only the
installed consumer directory**, with no archive registry, source checkout,
Composer cache or monorepo mounted. Composer locks are generated locally in
that temporary directory; they are not committed.

Inspect `$experiment_path/registry/manifest.json` for archive SHA-256 hashes,
member hashes, generator/verifier hashes and the supplied source commit. Each
ZIP contains only `composer.json`, `src/Probe/Probe.php`, `README.md` and `LICENSE`.
Retain the temporary directory for inspection or remove that exact directory
when finished; it contains only generated experiment files.

## Metadata and version selection

The generated registry has this shape:

```json
{
  "packages": {
    "paickage/distribution-fixture": {
      "0.1.0": {
        "name": "paickage/distribution-fixture",
        "version": "0.1.0",
        "dist": {
          "type": "zip",
          "url": "file:///exchange/registry/distribution-fixture-0.1.0.zip",
          "reference": "<source-commit-sha>",
          "shasum": "<archive-sha1>"
        }
      }
    }
  }
}
```

Actual entries also include the fixture's requirements, license and PSR-4
autoload mapping. `dist.shasum` is Composer's SHA-1 field; the independent
manifest also records SHA-256 hashes. These local hashes check integrity,
not publisher authenticity.

Both versions are available in the same `packages.json`. Consumer constraints
`^0.1.0` and `^0.2.0` resolve to `0.1.0` and `0.2.0` respectively. The consumer
repository configuration is:

```json
{
  "repositories": [
    {"type": "composer", "url": "file:///exchange/registry"},
    {"packagist.org": false}
  ]
}
```

There are no Composer path repositories or sibling-directory references. The
consumer configures its repository explicitly, as required by Composer's
[root-only repository configuration](https://getcomposer.org/doc/05-repositories.md#repositories).
See the verified [results and distribution limitations](../../planning/research/distribution-feasibility.md).

by-dolly

# Independent archive distribution feasibility

Issue: [#5](https://github.com/paickage/paickage/issues/5). This is a disposable
development experiment, not a package approval, release or registry deployment.

## Result

**Feasible locally.** Static Composer repository metadata and independent ZIP
archives installed the development-only `paickage/distribution-fixture` without
access to the monorepo. Two consumers independently resolved versions `0.1.0`
and `0.2.0` from one metadata file. No unrelated package or development dependency
was installed. The fixture is not listed as an available production package.

The reproducible [generator, verifier and commands](../../scripts/distribution/README.md)
are retained in the repository. The experiment writes only temporary files
outside the repository and does not create a `packages/` implementation.

## Evidence and acceptance coverage

The 2026-11-25 execution used Docker on Linux, Composer **2.11.1** running on
PHP **8.5.12**, and independent PHP **8.4.27** and **8.5.12** runtime containers. All three
phases used `--network none`. No credentials, network listeners or published
ports were supplied.

- The generator inspects each ZIP member against its original bytes. Each
  archive contains exactly `composer.json`, `src/Probe/Probe.php`, `README.md`
  and the fixture's MIT `LICENSE`; there is no root workspace or other package.
- `packages.json` contains explicit package name, version, requirements,
  autoloading, archive URL, source reference and Composer checksum. Both
  consumer configurations pass `composer validate --strict --no-check-publish`.
- Independent consumers use a `composer` repository at `file:///exchange/registry`
  and explicitly disable Packagist. `composer install --no-dev --prefer-dist
  --no-plugins --no-scripts --no-interaction --no-progress` resolves and extracts
  one fixture ZIP per consumer.
- Both consumers start without a lock file, vendor directory or shared Composer
  home/cache. Composer is allowed to create a local lock for the experiment;
  it is not committed.
- The install container has only generated experiment files mounted. It has no
  monorepo mount, source fixture directory, Composer path repository or sibling
  source references. Fixture source is created in-memory directly inside ZIPs.
- Final runtime verification has only the selected consumer directory mounted,
  read-only. Neither the monorepo nor even the distribution registry is available.
  Every installed fixture file matches the generator's SHA-256 manifest.
- The resolved installation source is `dist`; the runtime package list is
  exactly `["paickage/distribution-fixture"]`; development package count is zero.
  `Paickage\DistributionFixture\Probe::version()` returns the selected version.
- Guard checks also pass: generation refuses an already populated output
  directory; appending a byte to an installed source file makes the verifier
  fail with an installed-content mismatch. Restoring the file restores success.

Representative verified output:

```json
{
  "result": "pass",
  "php": "8.4.27",
  "package": "paickage/distribution-fixture",
  "version": "0.2.0",
  "installation_source": "dist",
  "public_api": "0.2.0",
  "runtime_packages": ["paickage/distribution-fixture"],
  "development_packages": 0
}
```

The complete generated manifests include archive SHA-256 hashes, installed-file
hashes, generator/verifier hashes and the source revision supplied when running
the experiment. The commands should be repeated from a committed revision;
the source reference alone does not prove an uncommitted working tree is clean.
No official release tag was created.

The committed-source repeat used
[`c4dff11ec314e0b159fea93999bb939beaeba59b`](https://github.com/paickage/paickage/commit/c4dff11ec314e0b159fea93999bb939beaeba59b)
with a clean working tree. Both Composer validations, both installs and all four
version/runtime combinations passed. Generated archive SHA-256 values were:

- `0.1.0`: `74dea5cf79db7384c1f0cb2328248fc4895961c03a6ad05b4141fdc945cdda29`
- `0.2.0`: `4a8064e704ef89d4fc6995da00c956549d7feebb54ccef37e2750fd6df6aa0e5`

These identify this fixture generation, not a published package release. Archive
byte reproducibility across ZIP library versions is not established.

## Distribution recommendation

Begin with static Composer metadata plus package-specific archives. It preserves
the single source repository and leaves individual libraries free of distribution
infrastructure dependencies. Composer's documented
[repository format](https://getcomposer.org/doc/05-repositories.md#composer)
supports explicit `name`, `version` and `dist` metadata. Consumers must configure
the repository at their project root; package dependencies cannot silently add
their own repositories. This experiment does not establish default-Packagist
discovery or package-prefixed Git-tag discovery.

Static HTTPS hosting is a later publication decision. At small scale its
operational work is generating/validating metadata and archives, retaining
immutable artifacts, publishing consistent updates, monitoring availability and
paying for storage/transfer where applicable. Costs have not been quoted or
authorized. A custom application registry adds server/authentication/database
maintenance and is not justified by this experiment. Satis is an alternative
development/distribution tool for generating static metadata if manual generation
becomes repetitive; it is not a runtime dependency of a library.

GitHub Release assets could hold independent archives in the existing repository,
but public asset availability, stable URLs, release/tag mapping and immutable
publication need separate validation and owner authorization. An external
distribution repository is unnecessary and is prohibited by the charter.

## Limits and remaining decisions

This proves local archive-based resolution, version selection and runtime
independence on the tested Linux/PHP runtimes. It does not prove public HTTPS
hosting, metadata atomicity, authenticated distribution, signing, CDN/cache
behavior, disaster recovery, public release automation or Windows compatibility.
It is not a performance benchmark or a guarantee about future production code.

SHA-1 and SHA-256 checks establish consistency with local metadata; without an
authenticated publication chain they do not establish publisher identity. The
fixture has no inter-package dependencies, so independent dependency-version
compatibility remains a pilot concern. Official package-qualified tags must later
be translated into explicit Composer version metadata; ordinary VCS discovery
was not used or claimed.

There is no unresolved local distribution-feasibility failure blocking the pilot.
Public registry/hosting selection and any package publication remain unauthorized.

by-dolly

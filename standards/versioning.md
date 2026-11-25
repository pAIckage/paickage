# Workspace branches and independent package releases

## Local interpretation

Workspace major **0** already exists in the verified issue/branch history. Retain
it under charter §30.2: `wip/0/{issue-number}-{slug}`. It identifies the development
workspace, not any package's public version. A package major change never changes
the workspace major or requires a new integration branch. Workspace-major changes
need a separate owner architectural decision.

Preserve the shared WIP → develop → staging → main workflow and PR protections.
Only `develop` currently exists; do not create promotion branches merely to fill
that diagram. Agents integrate complete issue branches through PRs to `develop`.
Promotion and main merges remain human-controlled. Repository-owned rulesets
require non-WIP PR updates, protect non-WIP deletion and prevent force pushes.
Wildcard naming filters approximate numeric/slug grammar; agents validate exact
issue-branch syntax themselves. No admin bypass actors are configured.

## Explicit charter override

The shared default expects one repository source version and a numeric tag on
each accepted releasable PR. The owner's PAiCKAGE charter §§30–32 establishes
independent package versions and official tags only after explicit release
authorization from an approved production `main` commit. Those provisions take
precedence here. Do not invent a synchronized root VERSION, bump unrelated
packages or tag a development integration to satisfy that shared assumption.

The root workspace is not a consumer library and needs no distributed root
version. Foundation/tooling/research integration does not publish a package.
Preserve unaffected shared issue, branch, PR, review and promotion safeguards.

## Package releases

Each package follows SemVer based on its own public API/behavior/compatibility.
Versions originate from verified package release records. Changelogs are
package-local. Dependency constraints must support independently released package
versions; atomic monorepo changes do not prove compatibility between those versions.

The authorized convention for official tags is `<slug>/v<MAJOR>.<MINOR>.<PATCH>`,
with SemVer prerelease suffixes when applicable. Tags are annotated, immutable,
traceable to the approved `main` commit and made only with explicit owner release
authorization. Never move/reuse a tag or make official tags from WIP/develop.

Distribution metadata maps a package tag to an ordinary Composer version, for
example `bytes/v1.2.0` → `paickage/bytes` version `1.2.0`. Consumers never need the
workspace branch-major. Ordinary monorepo Git/tag discovery is not proof of that
mapping; verify archive and metadata installation in the distribution experiment
and real pilot before release preparation.

Do not create public tags/releases, publish archives/registries/Packagist entries,
provision public hosting or incur costs without separate authorization. Local
fixture versions are development experiment data, not published releases.

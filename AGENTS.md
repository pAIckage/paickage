# PAiCKAGE development harness

PAiCKAGE is an independent, PHP-only ecosystem of small, complete packages for
humans and AI agents. Building the ecosystem is settled; research decides which
packages justify implementation. Do not introduce Moztopia branding or dependencies.

Read [architecture](standards/architecture.md) before architectural work,
[development](standards/development.md) before source/tooling changes and
[versioning](standards/versioning.md) before branch or release work.

- Use this repository, its linked Paickage Project and attached wiki only. Do not
  create package, planning, standards or distribution repositories/Projects/wikis.
- Production packages live in `packages/` only after explicit owner approval of
  purpose, scope/exclusions, layer, runtime dependencies, mandatory extensions
  and material constraints. Catalogue membership or an issue is not approval.
- Layer 0 has no runtime Composer package dependencies. Higher layers may use
  approved lower-layer PAiCKAGE packages only. No cycles, same-layer dependencies,
  vendored third-party implementations or root/sibling autoload shortcuts.
- PHP 8.4 is the minimum language baseline; current development/security floors
  and commands are in development standards. Native extensions must be declared;
  mandatory extensions require approval. Third-party development tools are allowed.
- Keep packages self-contained with their own Composer identity, namespace,
  source, tests, benchmarks, documentation, license and independent version.
- Avoid frameworks/adapters, UI, other implementation languages and custom native
  extensions. Use native secure cryptographic primitives. Do not wrap adequate
  PHP functionality merely to create a package.
- Use bounded issues and `wip/0/{issue-number}-{slug}` branches/worktrees. Workspace
  major **0** is an existing identifier, not a package version. Integrate through
  PRs to `develop`; preserve protections and human-controlled `main` promotion.
- Preserve task statuses exactly: Backlog → Ready → In progress → Testing → Done.
  Package lifecycle is separate; blockers/next actions belong in the Project.
- Catalogue identities/architecture live in `planning/catalog.json`; owner
  decisions in `planning/decisions/`; actual requirements in package Composer
  manifests. Validate agreement; do not overwrite an approved baseline with a proposal.
- Wiki pages explain and link repository authorities. Consumer installation/API
  documentation must ship inside each package. Advertise only verified availability.
  For the human guide/research, start at `wiki/Home.md` when the separate checkout
  is present and read the relevant pages explicitly. Root/linked standards remain
  available without the wiki; do not assume linked pages were loaded automatically.
- Run documented checks. Report automated coverage, architectural review and
  unknowns separately. Root tests do not establish isolated package correctness.
- Assign concurrent agents explicit responsibilities and separate branches or
  worktrees. Coordinate changes outside their boundary; preserve concurrent work.
- Do not publish packages, public archives/registries, releases or official tags
  without explicit authorization. Official package tags are annotated, immutable,
  package-qualified and made from an approved `main` commit.

Start with the distribution experiment and five bounded research assessments.
Recommend one narrow pilot and obtain approval before production implementation.
Add shared tooling and templates only when practical use justifies them.

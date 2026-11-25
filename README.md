# PAiCKAGE

**Small packages. Complete solutions. Built for AI.**

PAiCKAGE is an independent ecosystem of modern PHP libraries for human developers
and AI coding agents. One monorepo, one Project and one wiki support independently
installable, independently versioned packages. Minimum language baseline: PHP 8.4.
License: MIT.

No production package has been implemented, approved or published yet. The first
research candidates are Bytes, BigInt, Encoding, Random and Clock. These are
proposals, not installation promises.

- [Architecture and approval](standards/architecture.md)
- [Development commands](standards/development.md)
- [Workspace and package versioning](standards/versioning.md)
- [Package catalogue](planning/catalog.json) and [schema](planning/catalog.schema.json)
- [Paickage Project](https://github.com/orgs/paickage/projects/2)
- [Human guide](https://github.com/paickage/paickage/wiki)
- [Charter adoption record](planning/decisions/0001-monorepo-charter.md)
- [Five findings and pilot comparison](planning/research/pilot-comparison.md)
- [Local distribution feasibility](planning/research/distribution-feasibility.md)
- [Bytes pilot proposal — owner approval pending](planning/proposals/bytes-pilot.md)

The root Composer project is a development workspace. Consumers will install
individual packages through a distribution mechanism verified during the pilot;
they will not install this workspace. Public package publication needs separate
owner authorization.

Runtime packages have no third-party Composer dependencies. Layer 0 packages also
have no PAiCKAGE dependencies. Approved higher-layer packages may depend only on
approved lower-layer PAiCKAGE packages. PHP and declared native extensions form
the accepted foundation.

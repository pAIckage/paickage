# Production package boundary

Only packages with recorded owner architectural approval belong here. There are
currently no production packages. Research proposals remain in `planning/`.

An approved package is a self-contained Composer library with its own `src/`,
tests, benchmarks, `composer.json`, consumer documentation, package `AGENTS.md`,
AI usage reference, changelog and MIT license. Introduce those files when they
have real content. The namespace is `Paickage\<Package>`, with package-local PSR-4
autoloading; the Composer identity is `paickage/<slug>`.

Its archive must work without the root workspace, root development dependencies
or sibling source directories. Declare every runtime package/extension requirement
and reconcile it with the approved catalogue architecture. Root path repositories
help development; they do not prove independent distribution.

Disposable distribution fixtures belong under development tooling, never here or
in the catalogue as available production packages.

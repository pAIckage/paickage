# 0001 — Adopt the PAiCKAGE monorepo charter

Owner decision, supplied in this conversation on 2026-11-25. Source attachment
SHA-256: `9561d62adbd7c81ff0845b16d1a4a278dc3e5a93f2966708ede5bc22534fa2d0`.
The owner's final directive is to execute the charter through a minimum viable
monorepo, local independent-distribution experiment, five bounded research
assessments and an explicit pilot architectural approval.

Adopt one repository, Project and attached wiki; PHP-only independently owned
packages; zero third-party runtime Composer dependencies; approved lower-layer
dependencies only; strict package independence and explicit architectural approval.
No production package is approved by this ecosystem-level decision.

Preserve existing workspace major 0 under §30.2. Package versions are independent,
and official package-qualified annotated tags require separate authorization from
an approved `main` commit (§§30–32). The root workspace need not be published or
versioned as a consumer library. The exact shared-policy conflict and local
interpretation are documented in [versioning](../../standards/versioning.md).

Resource inspection found one organization repository (`pAIckage/paickage`), one
linked Paickage Project (number 2) and its initialized wiki. No previous planning
repository or extra Project was found in the accessible organization listing.
No prior work/resources were deleted or moved. Public visibility was explicitly
reconfirmed by the owner in this session before source publication.

The owner previously explicitly requested this wiki despite the shared Free-plan
business restriction and confirmed manual initialization. This session's charter
requires that same existing wiki. Reuse that explicit local exception; do not
change the shared restriction, organization plan, visibility or access. API
preflight on 2026-11-25 confirmed organization ownership and Free plan. Existing
Git default is `master`; maintenance remains in the ignored separate `wiki/` clone.

The connected GitHub App denied issue creation; the owner explicitly instructed
another attempt and the existing `mozrin` CLI connection succeeded. Continue with
that same authenticated account and scope, without credential/access changes.

Authoritative enforceable rules now live in `standards/`; catalogue architecture
and owner approval records stay separate from Project coordination state. This
record preserves the actual owner decisions; it is not approval of Bytes or any
other production package. Pilot approval must reference the concrete proposal
revision and the owner's subsequent explicit response.

by-dolly

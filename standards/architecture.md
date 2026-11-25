# Architecture and ownership

## Revision 0001

Initial owner-charter baseline adopted in decision 0001. Catalogue references
identify this revision; material changes must preserve/review approved baselines.

## Scope

PAiCKAGE owns a PHP-only, framework-independent ecosystem in this monorepo.
Packages solve one coherent problem completely within their approved scope.
Small scope does not imply a tiny implementation. Native PHP functionality must
be investigated before creating a package; adequate native APIs need no wrapper.

The root workspace, development tooling, Project and wiki are not runtime
dependencies. Do not create additional repositories, Projects or wikis. Framework
integration, adapters, interoperability packages, UI, scaffolding and other
implementation languages are outside current scope. Custom C extensions need
separate authorization following reproducible PHP benchmarks.

## Dependencies and layers

- No third-party runtime Composer dependencies or vendored implementations used
  to evade that restriction. Consult specifications and evaluate source licenses;
  copying third-party implementation source requires owner approval.
- Layer 0 depends only on PHP and explicitly approved mandatory native extensions.
- Layer N may depend only on approved PAiCKAGE packages with layer less than N.
  Record and approve dependencies before declaring compatible Composer constraints.
- No cycles, implicit sibling access, relative includes into other packages or
  root autoload rules masking incomplete package autoloading.
- Do not duplicate substantial functionality to evade a justified dependency or
  introduce a shared runtime library for development convenience.
- Development/testing dependencies are permitted but must not become runtime
  assumptions. Actual manifests and resolved runtime installations must agree
  with the approved catalogue architecture.

Use native PHP and extensions where appropriate. Declare mandatory `ext-*`
requirements and obtain owner approval; document optional extensions and test
equivalent observable behavior both with and without them. Pure-PHP fallbacks
must not silently become mandatory extension paths. Do not reimplement secure
cryptographic primitives supplied by PHP or use insecure randomness for secrets.

## Architectural approval

Research may recommend a package, scope, layer, dependencies or rejection. A
technically verified finding is not an owner-approved architectural decision.
Before production source is initialized, obtain explicit owner approval of:

1. Package purpose, scope and explicit exclusions.
2. Layer and runtime package dependencies, including a verified empty list.
3. Mandatory PHP extensions, including a verified empty list.
4. Material platform/architectural constraints and the reviewed proposal revision.

Record the owner's actual response and its reference in `planning/decisions/`;
link the proposal and exact Git revision. Approval of the ecosystem is not
approval of any candidate. Never invent an approval from a catalogue entry,
tracking issue, research completion or an agent recommendation.

Scope/layer/dependency/mandatory-extension changes, splits, consolidations and
fundamentally different architectures require reapproval. Routine algorithms,
private organization, fixes, tests and refactoring within approved boundaries
do not. Retain the approved baseline while a separate proposal awaits review.
Research, documentation and disposable experiments may continue meanwhile.

## Authorities

`standards/` owns enforceable ecosystem rules. `planning/catalog.json` owns
package identities and proposed/approved scope, layers, dependencies, extensions
and their independent verification states. `planning/decisions/` owns owner
approval records; implementation source and package Composer manifests describe
actual behavior and requirements. Validate discrepancies rather than silently
changing approvals to match implementation.

The GitHub Project owns lifecycle, priorities, task status, blockers and next
actions. Issues own actionable work. Package documentation owns public API usage;
verified release records own published versions. The wiki explains these sources
and links to them; it does not redefine them.

Architecture mirrors flow from catalogue/standards to Project/wiki. Coordination
snapshots flow from Project to generated exports. Release mirrors flow from
verified release records. No bidirectional synchronization or manually duplicated
dependency list is permitted. Manual one-way updates suffice initially.

## Catalogue interpretation

Each architecture facet has a state: `not_researched`, `proposed`, `verified`,
`approved`, `requires_review` or `rejected`. Unknown values are `null`, never an
empty dependency/extension list. `verified: []` means evidence supports absence;
`approved: []` additionally requires an owner approval reference. Unimplemented
packages have `directory: null`; do not create their production directories.

Store proposed revisions separately from an approved entry. Preserve rejected or
deferred research when useful; lifecycle and current next actions still belong
to the Project. Generate dependency representations from the catalogue and
preserve uncertainty and approval state in their output.

## Initial development gate

Complete the local distribution feasibility fixture and five bounded assessments
of Bytes, BigInt, Encoding, Random and Clock. Recommend one pilot, preferably
Bytes only if a narrow coherent API adds value beyond native operations. Obtain
owner approval before production implementation. Detailed specification follows
that approval; do not fully design all five packages or implement them together.

Every completed package needs comprehensive correctness/edge/error tests,
applicable standards vectors, reproducible benchmarks, static analysis and
isolated runtime installation. Review architecture in addition to automated
dependency checks: dynamic PHP and environmental assumptions cannot be proven
absent by a manifest validator. Report unverified areas explicitly.

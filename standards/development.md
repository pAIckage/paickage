# Development

## Environment and language

Use PHP 8.4+ with current approved security patches. This workspace currently
supports PHP 8.4.27+ within 8.4 and PHP 8.5.12+ within 8.5. PHP 8.4.27 is the
minimum test baseline. No CI, staging or production PHP runtime is configured;
do not claim verification of those environments. Container patch versions must
be reviewed when the environment changes.

Use installed tools when suitable; new tools belong in containers, not workstation
installations. Official `composer:2` and `php:8.4.27-cli-alpine` images are sufficient
for the initial workspace. No application/web server or network ingress is needed.

PHP source uses `declare(strict_types=1);`, native types, explicit control-flow
blocks, clear names and minimal abstraction. Use enums, readonly objects and
immutability where useful. Keep PHPDoc in multi-line summary-first form with a
blank line after `*/`. Use Pest syntax when introducing a test suite; no
PHPUnit-style test classes. Shared test/static-analysis/benchmark tools are added
when actual package work needs them.

## Initial commands

Run at the repository root (Docker required):

```sh
docker run --rm -u "$(id -u):$(id -g)" -v "$PWD:/app" -w /app composer:2 install --no-interaction --no-plugins --no-scripts
docker run --rm -u "$(id -u):$(id -g)" -v "$PWD:/app" -w /app composer:2 validate --strict
docker run --rm -u "$(id -u):$(id -g)" -v "$PWD:/app" -w /app composer:2 run validate-catalog
docker run --rm -u "$(id -u):$(id -g)" -v "$PWD:/app" -w /app composer:2 run dependency-graph
docker run --rm -u "$(id -u):$(id -g)" -v "$PWD:/app" -w /app composer:2 run test-tooling
docker run --rm -u "$(id -u):$(id -g)" -v "$PWD:/app" -w /app php:8.4.27-cli-alpine php scripts/validation/validate.php
```

Generated lockfiles and vendor trees remain local and ignored under the current
shared policy. Record resolved tooling versions in validation evidence; fresh
installs may resolve newer compatible versions. Root dependencies are development
tools only. Do not export root autoloading into production packages.

The initial validator checks the catalogue against JSON Schema, identity and
layer/dependency invariants, approval references and any implemented package's
declared runtime dependencies/extensions. Graph output is generated from that
same catalogue. This does not establish source-code independence, transitive
resolution, version-constraint compatibility or standards compliance; isolated
runtime verification and architectural review are separate gates during the pilot.

Opis JSON Schema and Pest are root development dependencies used for real schema
validation and focused tooling regression checks. They are not production package
requirements. The `phpunit.xml` configuration serves Pest's internal runner; tests
use Pest syntax. The installed Composer image runs the tooling suite; testing the
same suite on the minimum PHP line requires a container with its development-only
test extensions. The catalogue validator itself is also checked on bare PHP 8.4.27.

## Coordination and review

Track bounded work in issues and the linked Paickage Project. Preserve task
status capitalization/order. Package lifecycle is separate and applies primarily
to tracking issues. `Blocker` names the reason/resolver; `Next action` names the
action. Use `needs-owner-review` for outstanding architectural approval; Backlog
alone is not evidence of a review hold.

The root harness routes project-specific guidance. Each implemented package gets
its own agent instructions for approved boundaries, public API, tests and commands.
Assign concurrent agents explicit file/package boundaries and separate branches
or worktrees; shared files need coordination. Preserve unrelated edits/history.

Keep consumer installation/API documentation inside the independent package.
Add root/package AI discovery references when actual packages exist; never
advertise proposals as installable. Wiki guide changes must link authoritative
source revisions and describe actual, verified behavior.

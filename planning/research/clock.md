# Clock: bounded assessment

Research recommendation, not owner approval. Inspected 2026-11-25. Tracking: [#11](https://github.com/paickage/paickage/issues/11).

**Problem.** Code that directly reads the current date is difficult to test deterministically. An injectable clock can make expiry, scheduling decisions and time-dependent behavior reproducible without introducing a framework.

**Native coverage.** [`DateTimeImmutable`](https://www.php.net/manual/en/datetimeimmutable.construct.php) and `DateTimeZone` already represent wall-clock time and explicit UTC. They do not themselves provide an application-owned injectable frozen-clock contract. [`hrtime()`](https://www.php.net/manual/en/function.hrtime.php) measures monotonic time from an arbitrary origin; it is suitable for elapsed measurements, not interchangeable with a Unix timestamp. Its numeric return can depend on platform, so integer-nanosecond assumptions need a deliberate contract.

**Existing ecosystem.** [Lcobucci Clock](https://github.com/lcobucci/clock/tree/7d6f304e2a7f9dc50c22552d8fa685af87b815c2) demonstrates system and frozen clock implementations. Its pinned [manifest](https://github.com/lcobucci/clock/blob/7d6f304e2a7f9dc50c22552d8fa685af87b815c2/composer.json) requires `psr/clock`. That dependency provides an interoperability contract; the current PAiCKAGE scope explicitly chooses an owned API and excludes interoperability packages. Removing the dependency is possible but deliberately gives up that shared interface compatibility.

**Additional value and scope.** A small owned wall-clock interface with system, frozen and explicitly adjustable implementations would let PAiCKAGE consumers test “now” predictably. Return immutable UTC instants and define exactly how a test clock changes. Use native date/time operations rather than introducing a new date algebra. Begin with this wall-clock scope; treat monotonic measurement as a separate contract for later investigation, not a second interpretation of the same `now()` method.

**Exclusions.** No framework adapters, PSR interfaces, calendar arithmetic library, schedulers, sleep abstraction, timezone catalogue or global replacement of PHP time functions. A frozen wall clock must not promise to freeze the operating system or monotonic clock.

**Dependencies and layer.** Proposed Layer 0; no runtime Composer package dependency. Declare `ext-date` explicitly even though it is [built into standard PHP](https://www.php.net/manual/en/datetime.installation.php). No other mandatory or optional extension is currently justified. Layer 0 is a design recommendation, not approved package architecture or implemented-isolation assurance.

**Risk and recommendation.** Mutable test-clock ownership, wall-clock jumps, UTC consistency and confusing elapsed time with calendar time are the principal risks. Clock is the smallest fallback pilot, but it exercises less of the ecosystem's binary foundations. Defer implementation until a time-dependent consumer exists; choose it first only if Bytes' additional value or proposed platform constraint is rejected.

by-dolly

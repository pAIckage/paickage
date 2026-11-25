# Random: bounded assessment

Research recommendation, not owner approval. Inspected 2026-11-25. Tracking: [#10](https://github.com/paickage/paickage/issues/10).

**Problem.** Secure tokens, unbiased selection and repeatable test data are real needs, but the proposed list must be compared with modern PHP before creating another API.

**Native coverage.** [`Random\Randomizer`](https://www.php.net/manual/en/class.random-randomizer.php) already provides random bytes, bounded integers, selection of array keys, shuffled arrays and byte strings. Since PHP 8.3, [`getBytesFromString()`](https://www.php.net/manual/en/random-randomizer.getbytesfromstring.php) generates strings by sampling a supplied byte alphabet. Duplicate bytes intentionally weight that distribution; callers seeking uniform distinct symbols must supply each byte equally often. An omitted engine selects [`Random\Engine\Secure`](https://www.php.net/manual/en/random-randomizer.construct.php), while explicitly chosen pseudorandom engines support deterministic tests. All these capabilities are within the PHP 8.4 baseline.

**Existing ecosystem.** [RandomLib](https://github.com/ircmaxell/RandomLib/tree/e9e0204f40e49fa4419946c677eccd3fa25b8cf4) illustrates an older factory/source/mixer design offering several generator strengths. Its pinned [manifest](https://github.com/ircmaxell/RandomLib/blob/e9e0204f40e49fa4419946c677eccd3fa25b8cf4/composer.json) requires `ircmaxell/security-lib` as well as PHP. This historical design is evidence of an alternative architecture, not a requirement to reproduce it or a basis for recommending its security modes today.

**Additional value.** A PAiCKAGE package would need a demonstrated consumer requirement beyond the native methods, such as a well-defined token format with validated entropy policy. No such requirement has been established. A renamed Randomizer API or a second engine abstraction would conflict with the charter's rule against unnecessary wrappers.

**Provisional scope if revived.** Narrowly specified secure token/sampling policies over PHP's native engine. Exclude custom entropy sources, PRNG algorithms, cryptographic primitives, and any implicit production fallback to deterministic engines. Deterministic testing would be visibly separate from secure defaults; no complete API is proposed now.

**Dependencies and layer.** This provisional design could be Layer 0, with no runtime Composer packages and an explicit `ext-random` platform requirement. Whether that package is worth approving remains unresolved; no optional extension or inter-package dependency is presently justified.

**Risk and recommendation.** Engine substitution, biased alphabets, misleading entropy claims, and confusing characters with bytes are consequential security boundaries. Defer the package: native PHP already covers the initial candidate capabilities well. Revisit only when another approved package exposes a concrete unmet need. This rejects neither PAiCKAGE nor ownership of future higher-level security policy.

by-dolly

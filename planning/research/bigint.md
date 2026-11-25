# BigInt: bounded assessment

Research recommendation, not owner approval. Inspected 2026-11-25. Tracking: [#8](https://github.com/paickage/paickage/issues/8).

**Problem.** Exact integers larger than PHP's machine range are a genuine foundational capability for future decimal and rational arithmetic. Their values must survive parsing, calculation and serialization without floating-point coercion.

**Native coverage.** PHP [`int`](https://www.php.net/manual/en/language.types.integer.php) is platform-sized and signed; overflowing operations may become floats. [GMP](https://www.php.net/manual/en/book.gmp.php) supports arbitrary-length integers, while [BCMath](https://www.php.net/manual/en/book.bc.php) supplies string-based arbitrary-precision arithmetic. These native extensions are accepted foundations, but neither may be mandatory under the proposed pure-PHP default. Native machine arithmetic alone does not satisfy arbitrary precision.

**Existing ecosystem.** [Brick Math](https://github.com/brick/math/tree/ee4c0fbc8846e5aca58c8983f6f7d285de09ef89) already supplies immutable integer, decimal and rational values, with a pure-PHP fallback and optional GMP/BCMath acceleration. Its pinned [manifest](https://github.com/brick/math/blob/ee4c0fbc8846e5aca58c8983f6f7d285de09ef89/composer.json) requires only PHP. Do not claim that this established solution requires a fragmented runtime dependency chain.

**Additional value.** PAiCKAGE's justification would be ownership of a deliberately integer-only foundation, explicit conversion/error contracts, and independently verified behavior for later PAiCKAGE arithmetic. A smaller implementation is an opportunity to investigate, not a demonstrated performance or correctness advantage over Brick.

**Proposed scope.** Canonical signed integer values; lossless decimal parsing/output; comparison; addition, subtraction, multiplication, quotient/remainder, integer exponentiation, modular arithmetic, greatest common divisor and bounded base conversions. Detailed division-sign conventions and resource policies must be specified later. Exclude decimal/fraction/money types, floating-point inputs, primality/key generation and any constant-time or cryptographic guarantees.

**Dependencies and layer.** A standalone pure-PHP algorithm can operate on bounded limbs or digit sequences using core operations; no runtime Composer dependency or mandatory extra extension is technically necessary for this scope. Proposed Layer 0. GMP and BCMath are possible optional accelerators only after behavior parity and practical value justify them; do not make them implicit requirements. These are design findings, not an implemented package's dependency audit.

**Risk and recommendation.** Carry propagation, signed division, large powers, memory growth and adversarial input sizes create a much larger verification burden than Bytes. Differential tests against independent development-only oracles and arithmetic properties will be necessary. Defer production implementation until the pilot establishes the workflow and a concrete downstream arithmetic need is selected; retain BigInt as a justified future foundation rather than the first pilot.

by-dolly

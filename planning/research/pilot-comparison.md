# First pilot comparison

Recommendation prepared 2026-11-25 under [research task #6](https://github.com/paickage/paickage/issues/6). No package architecture is owner-approved and none is implemented or advertised for installation.

[Bytes](bytes.md) is the strongest first pilot: a small, testable contract for exact binary ranges and checked fixed-width unsigned fields can remove repeated correctness decisions from callers. Native PHP already supplies the mechanics. The proposed value is enforcing coherent invariants at their boundary, with a strict scope that excludes generalized binary processing.

[Clock](clock.md) has the lowest implementation burden and is the fallback if Bytes is rejected. Its injectable wall-clock contract is useful, but it exercises fewer foundational operations and currently lacks an immediate selected consumer.

[Encoding](encoding.md) has an objective specification and useful canonical-decoding contracts. It is a reasonable later primitive for identifiers and payloads; a concrete consumer should establish which named profiles earn inclusion. It does not intrinsically require Bytes or BigInt.

[BigInt](bigint.md) has substantial downstream value for future decimal and rational arithmetic, but signed arithmetic, division and resource limits create the highest correctness burden here. Brick already demonstrates that a dependency-free pure-PHP design is possible. Ownership and a focused integer boundary are valid ecosystem choices; performance superiority has not been established. It should follow the pilot rather than determine the pilot's tooling burden.

[Random](random.md) has the weakest current justification. PHP 8.4 already supplies the requested secure sampling and deterministic-engine mechanisms. Defer until a specific higher-level policy is required rather than introduce a native-function wrapper.

The resulting recommendation is **Bytes first; Clock as fallback; Encoding and BigInt when approved consumers justify them; Random deferred pending additional value**. This is a technical recommendation, not a change to Project priority or lifecycle by itself. The GitHub Project remains authoritative for scheduling and coordination.

The [proposed Bytes boundary](../proposals/bytes-pilot.md) is ready for review. Before substantial production implementation, the coordinator must attach the actual result from the separate [distribution experiment #5](https://github.com/paickage/paickage/issues/5) and obtain an explicit owner decision referencing the proposal revision. This document does not assert that another agent's experiment passed.

by-dolly

# Bytes pilot: proposed architectural boundary

Proposal revision: **1**, prepared 2026-11-25. Package: `paickage/bytes`; namespace: `Paickage\Bytes`; tracking [#7](https://github.com/paickage/paickage/issues/7). **Owner approval: pending.** There is no production package directory or public release.

## Purpose and value

Provide a small immutable binary value with exact-range operations and checked fixed-width unsigned fields. The [assessment](../research/bytes.md) demonstrates native truncation and overflow behaviors that callers otherwise need to handle repeatedly. The package will own those validation contracts while using PHP's native binary mechanics.

Potential future consumers include UUID/ULID field handling and binary readers. They are possibilities, not approved packages or guaranteed dependencies. Encoders may continue accepting ordinary binary strings directly.

## Proposed scope

- Construct from and expose an ordinary binary PHP string without text interpretation; preserve empty input and NUL bytes.
- Report byte length, compare exact byte content, concatenate sequences, and take exact offset/length slices.
- Replace a fixed-width byte range by returning a new value while preserving the original.
- Read and replace unsigned 8-, 16- and 32-bit fields. Require explicit big/little endianness for multibyte fields, with host-independent observable results.
- Reject invalid offsets, incomplete ranges, width mismatches and out-of-range integers before conversion. No silent padding, truncation, wrapping or float conversion.

For example, changing a two-byte integer inside a header must either return a complete new byte sequence or fail explicitly; a short header and a value above `65535` must never silently produce an apparently successful replacement. Method/class names and exact exception contracts belong to the detailed specification after approval.

## Explicit exclusions

Signed integers; full-range unsigned-64 values; arbitrary-precision arithmetic; floating-point fields; individual bit-field APIs; mutable buffers and cursors; general streams; file/network I/O; format/protocol parsers; serialization; binary-to-text encoding; cryptography; compression; native acceleration; framework or external-interface adapters.

## Proposed architecture and requirements

- **Layer:** 0.
- **Runtime Composer packages:** none, including PAiCKAGE siblings.
- **Mandatory additional PHP extensions:** none.
- **PHP platform:** PHP 8.4 or newer within the workspace's supported security patch policy; **64-bit PHP is a proposed material requirement** so every unsigned-32 result fits a native `int`. Declare Composer's `php-64bit` platform requirement and guard it at runtime; do not describe it as a downloaded runtime library.
- **Implementation constraints:** original strict PHP source, immutable public values, explicit typed inputs, native binary primitives, no shared root runtime, and extraction/installability independent of the monorepo.
- **Compatibility:** integer conversions stop at unsigned 32 bits. Future signed/64-bit support is a separate scope decision, not implied by storing an arbitrary byte sequence.

## Principal risks and verification

Review offset-plus-length overflow without introducing unsafe intermediate arithmetic; test exact boundaries and empty ranges. Verify all integer extrema and both endian orders using published native format definitions and hand-checked vectors. Property tests should exercise read/write round trips, immutability and exact slicing/replacement invariants. Benchmarks must measure representative sizes and copying costs, with no zero-copy or speed claims before evidence. Test supported PHP environments and clean archive installation separately from monorepo tests. No timing or cryptographic security claim is made.

## Decision requested

The owner must explicitly approve or amend revision 1's purpose, scope, exclusions, Layer 0 classification, empty runtime package dependency list, empty mandatory-extension list, 64-bit platform requirement and material constraints. Record the actual owner response in the repository's approval records; this proposal is not that approval.

Substantial implementation also requires a demonstrated distribution feasibility result. Public package publication and official release tags require separate explicit authorization even after architectural approval.

by-dolly

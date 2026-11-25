# Bytes: bounded assessment

Research recommendation, not owner approval. Inspected 2026-11-25. Tracking: [#7](https://github.com/paickage/paickage/issues/7).

**Problem.** Repeated binary-field handling needs explicit bounds, integer ranges and byte order. A useful package would establish these contracts consistently rather than rename native functions.

**Native coverage.** PHP already supplies binary strings, slicing, bitwise operations, [`pack()`](https://www.php.net/manual/en/function.pack.php) and [`unpack()`](https://www.php.net/manual/en/function.unpack.php). Their contracts differ: slicing can truncate, packing an out-of-range integer can discard high bits, and unpacking can fail. A PHP 8.4.27, 64-bit container probe returned `"c"` for `substr('abc', 2, 2)`, `"00"` for `bin2hex(pack('C', 256))`, and `false` for a truncated unsigned-16 unpack. These are native behaviors, not native defects.

**Existing ecosystem.** [BinaryStream](https://github.com/wapmorgan/BinaryStream/tree/cfe40bc12a5d5a446f9b20131957bae78fc47a12) already offers endian-aware reads/writes, streams, bit fields and grouped formats. Its [manifest](https://github.com/wapmorgan/BinaryStream/blob/cfe40bc12a5d5a446f9b20131957bae78fc47a12/composer.json) requires only PHP. Therefore dependency removal alone does not justify PAiCKAGE Bytes; a smaller, modern, predictable contract does.

**Additional value and scope.** Recommend an immutable byte sequence with exact slicing, concatenation, fixed-width replacement, and checked unsigned 8/16/32-bit reads/writes. Endian-sensitive operations must require explicit big/little byte order. Negative offsets, truncated ranges and integer overflow fail consistently before native conversion. For example, replacing a two-byte field at an offset either succeeds completely in a new value or leaves the original untouched and fails explicitly.

**Exclusions.** No signed integers, full-range unsigned-64 conversion, floats, cursors/streams, arbitrary-precision arithmetic, encoding, cryptography, compression, serialization, protocol or file-format parsers in the pilot.

**Dependencies and layer.** Proposed Layer 0; this narrow design needs no runtime Composer packages or additional PHP extensions. Propose 64-bit PHP so the entire unsigned-32 range fits a PHP `int`; [`php-64bit`](https://getcomposer.org/doc/articles/composer-platform-dependencies.md) can declare that platform requirement. It is a material constraint requiring owner approval, not an ecosystem-wide assumption.

**Risk and recommendation.** Bounds arithmetic, accidental native truncation and copying large strings need focused tests and benchmarks. [`int` is signed and platform-dependent](https://www.php.net/manual/en/language.types.integer.php); do not imply unsigned-64 support. Recommend Bytes as the first pilot, conditional on approval of the [bounded proposal](../proposals/bytes-pilot.md) and demonstrated distribution feasibility. No implementation independence has yet been tested.

by-dolly

# Encoding: bounded assessment

Research recommendation, not owner approval. Inspected 2026-11-25. Tracking: [#9](https://github.com/paickage/paickage/issues/9).

**Problem.** Binary identifiers and payloads need named alphabets, explicit padding policy and predictable rejection of malformed or noncanonical text. Merely adding another Base64 wrapper would provide little value.

**Native coverage.** PHP supplies [`bin2hex()`](https://www.php.net/manual/en/function.bin2hex.php), [`hex2bin()`](https://www.php.net/manual/en/function.hex2bin.php), Base64 encoding/decoding, and [Sodium's named Base64 variants](https://www.php.net/manual/en/function.sodium-bin2base64.php). [`base64_decode(..., true)`](https://www.php.net/manual/en/function.base64-decode.php) documents alphabet rejection, not a complete canonical-format contract. A PHP 8.4.27 probe decoded noncanonical `Zh==` as `f`, whose canonical encoding is `Zg==`. Do not confuse native strict mode with the proposed stronger canonical profile.

**Existing ecosystem.** [Paragonie's constant-time encoding](https://github.com/paragonie/constant_time_encoding/tree/75197dc1c577142c53a5e6d30c6ba32441513486) covers RFC 4648 encodings and requires only PHP in its [manifest](https://github.com/paragonie/constant_time_encoding/blob/75197dc1c577142c53a5e6d30c6ba32441513486/composer.json). Its timing-oriented goals are distinct; PAiCKAGE must not imply equivalent timing guarantees without evidence.

**Additional value and scope.** A cohesive RFC 4648 family could share exact error and canonicalization policies across Base16, Base32, Base32Hex, Base64 and Base64URL. Specify padding, alphabet case, whitespace and unused pad-bit behavior per named profile. [RFC 4648 §§3, 4–8 and 10](https://www.rfc-editor.org/rfc/rfc4648.html) provide normative behavior and test vectors. This is more useful than separate packages per encoding alphabet.

**Exclusions.** [Crockford Base32](https://www.crockford.com/base32.html) has different human-input alias/check-symbol rules; Base58 needs a named alphabet and leading-zero policy rather than an unspecified universal variant. Keep both as researched follow-on candidates, outside the first RFC-family scope. Exclude numeric base-conversion APIs, streaming frameworks, cryptography, and promised constant-time processing.

**Dependencies and layer.** Proposed Layer 0, no runtime Composer packages or mandatory additional extensions. Fixed-radix codecs can use bounded bit accumulators; even Base58 can operate bytewise rather than require BigInt. That establishes design feasibility, not proof of any future implementation. Sodium need not be mandatory for a core-PHP RFC-family implementation.

**Risk and recommendation.** Ambiguous accepted text, malformed padding, nonzero unused bits and decoder resource use are the main correctness risks. Recommend further specification after the pilot, driven by an actual identifier/payload consumer. Do not add a dependency on Bytes simply because both deal with binary strings; independence is feasible.

by-dolly

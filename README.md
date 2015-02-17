Hash and encrypt, PHP examples
==============================

Example of an encrypted password hash storage in PHP, uses bcrypt for hashing and AES-256 in CBC mode for encryption.
**Do not** encrypt just the passwords, encrypt only password hashes for extra security.

Uses OpenSSL extension, `ext/mcrypt` has unmaintained dependency and was almost [removed from core for PHP 7](https://wiki.php.net/rfc/removal_of_dead_sapis_and_exts#extmcrypt).
Compatibility with `ext/mcrypt`'s default Zero Byte Padding is supported.

## Padding
CBC (*cipher-block chaining*) mode requires input that is a multiple of the block size (128-bits for AES), so the hashed password has to be *padded* to bring it to required length.
`openssl_encrypt` uses PKCS#7 padding by default or *no padding* if the `OPENSSL_ZERO_PADDING` option is specified.
`mcrypt_encrypt` uses non-standard zero byte padding. To support it with OpenSSL functions we have to use the `OPENSSL_ZERO_PADDING` option and implement custom padding.

## Files
- [`example-encrypthash.php`](example-encrypthash.php) - Encrypted password hash storage, uses bcrypt + AES-256-CBC PKCS#7 padding or ZeroBytePadding for ext/mcrypt compatibility.
- [`example-hash.php`](example-hash.php) - Password hash storage, uses bcrypt.
- [`functions-encrypthash.php`](functions-encrypthash.php) - Functions used by `example-encrypthash.php`
- [`test/encrypthash.php`](test/encrypthash.php) - Tests for encrypted hash functions
- [`test/hash.php`](test/hash.php) - Tests for hash functions

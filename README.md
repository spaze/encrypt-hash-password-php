Hash and encrypt, PHP examples
==============================

Example of an encrypted password hash storage in PHP, uses bcrypt for hashing and AES-128 in CBC mode for encryption. It uses [defuse/php-encryption](https://github.com/defuse/php-encryption) package for crypto operations.
**Do not** encrypt just the passwords, encrypt only password hashes for extra security.

## Usage

- Install [defuse/php-encryption](https://github.com/defuse/php-encryption) via [Composer](https://packagist.org/packages/defuse/php-encryption) first, or at least copy the `Crypto.php` file to your project
- Don't write your own encryption functions

## Key
Generate 128-bit key (in PHP hexdec-chars string) using

- `echo preg_replace('/(..)/', '\x$1', bin2hex(openssl_random_pseudo_bytes(16)));`
- or by running `openssl rand -hex 16 | sed s/\\\(..\\\)/\\\\x\\1/g` in `bash`

The key should be stored in the following format: `"\xf3\x49\xf9\x4a\x0a\xb2 ..."`. Do NOT encode the `$key` with `bin2hex()` or `base64_encode()` or similar, they may leak the key to the attacker through side channels.

## Files

- [`example-encrypthash.php`](example-encrypthash.php) - Encrypted password hash storage, uses bcrypt + AES-128-CBC with PKCS#7 padding and SHA-256 HMAC authentication using *Encrypt-then-MAC* approach
- [`example-hash.php`](example-hash.php) - Password hash storage, uses bcrypt.
- [`functions-encrypthash.php`](functions-encrypthash.php) - Functions used by `example-encrypthash.php`
- [`tests/encrypthash.php`](tests/encrypthash.php) - Tests for encrypted hash functions
- [`tests/hash.php`](tests/hash.php) - Tests for hash functions

## Tests
Simple tests are included, run them with `php tests/hash.php` and `php tests/encrypthash.php`.

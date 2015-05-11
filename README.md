Hash and encrypt, PHP examples
==============================

Example of an encrypted password hash storage in PHP, uses bcrypt for hashing and AES-128 in CBC mode for encryption. It uses [defuse/php-encryption](https://github.com/defuse/php-encryption) package for crypto operations.
**Do not** encrypt just the passwords, encrypt only password hashes for extra security.

## Usage
- Install [defuse/php-encryption](https://github.com/defuse/php-encryption) first
- Don't write your own encryption functions

## Files
- [`example-encrypthash.php`](example-encrypthash.php) - Encrypted password hash storage, uses bcrypt + AES-128-CBC PKCS#7 padding.
- [`example-hash.php`](example-hash.php) - Password hash storage, uses bcrypt.
- [`functions-encrypthash.php`](functions-encrypthash.php) - Functions used by `example-encrypthash.php`
- [`tests/encrypthash.php`](tests/encrypthash.php) - Tests for encrypted hash functions
- [`tests/hash.php`](tests/hash.php) - Tests for hash functions

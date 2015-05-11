<?php
/**
 * Encrypted password hash storage functions.
 *
 * Uses bcrypt + AES-128-CBC PKCS#7 padding.
 *
 * Requires OpenSSL PHP extension.
 *
 * @author Michal Špaček <https://www.michalspacek.cz>
 */

/**
 * Hash a password and then encrypt the hash.
 *
 * @param string $password The password
 * @param string $keyHex The hexdec key (64 chars, 0-9, A-F)
 * @return string
 */
function hashAndEncrypt($password, $keyHex)
{
	$hash = password_hash($password, PASSWORD_DEFAULT);

	$key = pack('H32', $keyHex);

	require_once 'Crypto.php';
	try {
		$ciphertext = Crypto::Encrypt($hash, $key);
	} catch (CryptoTestFailedException $e) {
		// don't throw an exception, it will contain sensitive data
		die('Cannot safely perform encryption');
	} catch (CannotPerformOperationException $e) {
		// don't throw an exception, it will contain sensitive data
		die('Cannot safely perform decryption');
	}
	return base64_encode($ciphertext);
}

/**
 * Decrypt hash and verify that it matches a password.
 *
 * @param string $password The user's password
 * @param string $ciphertextBase64 Encrypted hash created by hashAndEncrypt()
 * @param string $keyHex The encryption key
 * @return boolean
 */
function decryptAndVerifyHash($password, $ciphertextBase64, $keyHex)
{
	$key = pack('H32', $keyHex);
	$ciphertext = base64_decode($ciphertextBase64);

	require_once 'Crypto.php';
	try {
		$decryptedHash = Crypto::Decrypt($ciphertext, $key);
	} catch (InvalidCiphertextException $e) { // VERY IMPORTANT
		// Either:
		//   1. The ciphertext was modified by the attacker,
		//   2. The key is wrong, or
		//   3. $ciphertext is not a valid ciphertext or was corrupted.
		// Assume the worst.
		// don't throw an exception, it will contain sensitive data
		die('DANGER! DANGER! The ciphertext has been tampered with!');
	} catch (CryptoTestFailedException $e) {
		// don't throw an exception, it will contain sensitive data
		die('Cannot safely perform encryption');
	} catch (CannotPerformOperationException $e) {
		// don't throw an exception, it will contain sensitive data
		die('Cannot safely perform decryption');
	}

	return password_verify($password, $decryptedHash);
}

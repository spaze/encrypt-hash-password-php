<?php
/**
 * Encrypted password hash storage functions.
 *
 * Uses bcrypt + AES-256-CBC PKCS#7 padding or ZeroBytePadding for MCrypt compatibility.
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
 * @param boolean $mcryptCompatibility Whether the result should be compatible with mcrypt_encrypt
 * @return string
 */
function hashAndEncrypt($password, $keyHex, $mcryptCompatibility = false)
{
	$hash = password_hash($password, PASSWORD_DEFAULT);

	$cipher = 'AES-256-CBC';
	$key = pack('H64', $keyHex);
	$options = OPENSSL_RAW_DATA;
	if ($mcryptCompatibility) {
		$options |= OPENSSL_ZERO_PADDING;
		$hash .= str_repeat("\0", 16 - (strlen($hash) % 16));
	}
	$iv = createIv(openssl_cipher_iv_length($cipher));
	$encryptedHash = openssl_encrypt($hash, $cipher, $key, $options, $iv);
	return base64_encode($iv . $encryptedHash);
}

/**
 * Decrypt hash and verify that it matches a password.
 *
 * @param string $password The user's password
 * @param string $encryptedBase64 Encrypted hash created by hashAndEncrypt()
 * @param string $keyHex The encryption key
 * @param boolean $mcryptCompatibility Whether the input is compatible with mcrypt_encrypt
 * @return boolean
 */
function decryptAndVerifyHash($password, $encryptedBase64, $keyHex, $mcryptCompatibility = false)
{
	$encrypted = base64_decode($encryptedBase64);

	$cipher = 'AES-256-CBC';
	$key = pack('H64', $keyHex);
	$options = OPENSSL_RAW_DATA;
	if ($mcryptCompatibility) {
		$options |= OPENSSL_ZERO_PADDING;
	}
	$ivSize = openssl_cipher_iv_length($cipher);
	$iv = substr($encrypted, 0, $ivSize);
	$encrypted = substr($encrypted, $ivSize);
	$decryptedHash = openssl_decrypt($encrypted, $cipher, $key, $options, $iv);
	if ($mcryptCompatibility) {
		$decryptedHash = rtrim($decryptedHash, "\0");
	}
	return password_verify($password, $decryptedHash);
}

/**
 * Create an initialization vector (IV).
 *
 * @param integer $length IV length in bytes
 * @return string
 */
function createIv($length)
{
	$random = false;
	$strong = false;

	$i = 0;
	while (!$strong && $i < 10) {
		$random = openssl_random_pseudo_bytes($length, $strong);
		$i++;
	}

	if ($random === false) {
		throw new \RuntimeException("Error creating IV, tried $i times");
	}

	return $random;
}

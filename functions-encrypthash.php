<?php
/**
 * Encrypted password hash storage functions.
 *
 * Uses bcrypt + AES-256-CBC PKCS#7 padding.
 *
 * Requires OpenSSL PHP extension.
 *
 * @author Michal Špaček <https://www.michalspacek.cz>
 */

function encryptAndHash($password, $keyHex)
{
	$hash = password_hash($password, PASSWORD_DEFAULT);

	$cipher = 'AES-256-CBC';
	$key = pack('H64', $keyHex);
	$options = OPENSSL_RAW_DATA;
	$iv = createIv(openssl_cipher_iv_length($cipher));
	$encryptedHash = openssl_encrypt($hash, $cipher, $key, $options, $iv);
	return base64_encode($iv . $encryptedHash);
}

function decryptAndVerifyHash($password, $encryptedBase64, $keyHex)
{
	$encrypted = base64_decode($encryptedBase64);

	$cipher = 'AES-256-CBC';
	$key = pack('H64', $keyHex);
	$options = OPENSSL_RAW_DATA;
	$ivSize = openssl_cipher_iv_length($cipher);
	$iv = substr($encrypted, 0, $ivSize);
	$encrypted = substr($encrypted, $ivSize);
	$decryptedHash = openssl_decrypt($encrypted, $cipher, $key, $options, $iv);
	return password_verify($password, $decryptedHash);
}

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

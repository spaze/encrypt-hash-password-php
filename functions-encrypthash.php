<?php
/**
 * Encrypted password hash storage functions.
 *
 * Uses bcrypt + AES-256-CBC ZeroBytePadding.
 *
 * Requires mcrypt PHP extension.
 *
 * @author Michal Špaček <https://www.michalspacek.cz>
 */

function encryptAndHash($password, $keyHex)
{
	$hash = password_hash($password, PASSWORD_DEFAULT);

	$key = pack('H64', $keyHex);
	$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_DEV_URANDOM);
	$encryptedHash = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $hash, MCRYPT_MODE_CBC, $iv);
	return base64_encode($iv . $encryptedHash);
}

function decryptAndVerifyHash($password, $encryptedBase64, $keyHex)
{
	$encrypted = base64_decode($encryptedBase64);

	$key = pack('H64', $keyHex);
	$ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
	$iv = substr($encrypted, 0, $ivSize);
	$encrypted = substr($encrypted, $ivSize);
	$decryptedHash = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $encrypted, MCRYPT_MODE_CBC, $iv);
	$decryptedHash = rtrim($decryptedHash, "\0");

	return password_verify($password, $decryptedHash);
}

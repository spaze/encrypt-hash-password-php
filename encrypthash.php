<?php
/**
 * Encrypted password hash storage.
 *
 * Uses bcrypt + AES-256-CBC ZeroBytePadding.
 *
 * Requires mcrypt PHP extension.
 *
 * Set your password database column to VARCHAR(255) or similar.
 * Generate 256-bit key (64 chars, 0-9, A-F) using
 * <code>echo current(unpack('H64', mcrypt_create_iv(32, MCRYPT_DEV_RANDOM)));</code>
 * Store the key in a configuration file.
 *
 * @author Michal Špaček <http://www.michalspacek.cz>
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


// Hash, encrypt and store password
// ********************************

$key = '...'; // Loaded from a config file
$encrypted = encryptAndHash($_POST['password'], $key);

// Example only
$statement = $pdo->prepare('INSERT INTO users (user, password) VALUES (?, ?)');
$statement->execute(array($_POST['user'], $encrypted));


// Verify that a password matches an encrypted hash
// ************************************************

$key = '...'; // Loaded from a config file

// Example only
$statement = $pdo->prepare('SELECT password FROM users WHERE user = ?');
$statement->execute(array($_POST['user']));
$encrypted = $statement->fetchColumn();

if (decryptAndVerifyHash($_POST['password'], $encrypted, $key)) {
	// Logged in
} else {
	// Wrong password
}

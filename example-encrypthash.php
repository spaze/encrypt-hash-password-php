<?php
/**
 * Encrypted password hash storage example.
 *
 * Uses bcrypt + AES-128-CBC PKCS#7 padding.
 *
 * Requires openssl PHP extension.
 *
 * Set your password database column to VARCHAR(255) or similar.
 * Generate 128-bit key (in PHP hexdec-chars string) using
 * <code>echo preg_replace('/(..)/', '\x$1', bin2hex(openssl_random_pseudo_bytes(16)));</code>
 * or by running <code>openssl rand -hex 16 | sed s/\\\(..\\\)/\\\\x\\1/g</code> in <code>bash</code>
 * Store the key in a configuration file.
 *
 * @author Michal Špaček <https://www.michalspacek.cz>
 */

require_once './functions-encrypthash.php';

// Hash, encrypt and store password
// ********************************

$key = "..."; // Loaded from a config file, eg. $key = "\xf3\x49\xf9\x4a\x0a\xb2 ...";
$encrypted = hashAndEncrypt($_POST['password'], $key);

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

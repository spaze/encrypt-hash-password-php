<?php
/**
 * Password hash storage.
 *
 * Uses bcrypt.
 *
 * Requires PHP 5.5 or newer.
 *
 * Set your password database column to VARCHAR(255) or similar.
 *
 * @author Michal Špaček <http://www.michalspacek.cz>
 */


/*
For PHP 5.3.7 - 5.5.0:
Install password_compat using composer install ircmaxell/password_compat
or download https://github.com/ircmaxell/password_compat/blob/master/lib/password.php
and require_once 'password.php'
*/


// Hash and store password
// ***********************

$hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

// Example only
$statement = $pdo->prepare('INSERT INTO users (user, password) VALUES (?, ?)');
$statement->execute(array($_POST['user'], $hash));


// Verify that a password matches a hash
// *************************************

$statement = $pdo->prepare('SELECT password FROM users WHERE user = ?');
$statement->execute(array($_POST['user']));
$hash = $statement->fetchColumn();

if (password_verify($_POST['password'], $hash)) {
	// Logged in
} else {
	// Wrong password
}

<?php
// Run with php encrypthash.php
require_once './functions-encrypthash.php';

$keyHex = current(unpack('H64', mcrypt_create_iv(32, MCRYPT_DEV_URANDOM)));
$plainText = 'Haxx0r ipsum tera dereference rsa ascii foo bypass flush ip';


echo 'Test: ';
$encrypted = hashAndEncrypt($plainText, $keyHex);
echo (decryptAndVerifyHash($plainText, $encrypted, $keyHex) === true ? 'OK' : 'FAILED');
echo PHP_EOL;

if (!extension_loaded('mcrypt')) {
	echo 'MCrypt extension not loaded, cannot test MCrypt compatibility';
	echo PHP_EOL;
	exit;
}

echo 'Test mcrypt compatibility, encrypt OpenSSL, decrypt MCrypt: ';
// encrypt using OpenSSL
$encrypted = hashAndEncrypt($plainText, $keyHex, true);
// decrypt using MCrypt
$encrypted = base64_decode($encrypted);
$ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
$iv = substr($encrypted, 0, $ivSize);
$encrypted = substr($encrypted, $ivSize);
$decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, pack('H64', $keyHex), $encrypted, MCRYPT_MODE_CBC, $iv);
$decrypted = rtrim($decrypted, "\0");
echo (password_verify($plainText, $decrypted) === true ? 'OK' : 'FAILED');
echo PHP_EOL;


echo 'Test mcrypt compatibility, encrypt MCrypt, decrypt OpenSSL: ';
// encrypt using MCrypt
$hash = password_hash($plainText, PASSWORD_DEFAULT);
$key = pack('H64', $keyHex);
$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_DEV_URANDOM);
$encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $hash, MCRYPT_MODE_CBC, $iv);
$encrypted = base64_encode($iv . $encrypted);
// decrypt using OpenSSL
echo (decryptAndVerifyHash($plainText, $encrypted, $keyHex, true) === true ? 'OK' : 'FAILED');
echo PHP_EOL;

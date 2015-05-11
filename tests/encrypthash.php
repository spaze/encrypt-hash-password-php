<?php
// Run with php tests/encrypthash.php
require_once './functions-encrypthash.php';

$keyHex = current(unpack('H32', mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)));
$plainText = 'Haxx0r ipsum tera dereference rsa ascii foo bypass flush ip';


echo 'Test: ';
$encrypted = hashAndEncrypt($plainText, $keyHex);
echo (decryptAndVerifyHash($plainText, $encrypted, $keyHex) === true ? 'OK' : 'FAILED');
echo PHP_EOL;

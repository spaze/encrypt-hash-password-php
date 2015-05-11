<?php
// Run with php tests/encrypthash.php
require_once './functions-encrypthash.php';

$keyHex = current(unpack('H32', openssl_random_pseudo_bytes(16)));
$plainText = 'Haxx0r ipsum tera dereference rsa ascii foo bypass flush ip';


echo 'Test: ';
$encrypted = hashAndEncrypt($plainText, $keyHex);
echo (decryptAndVerifyHash($plainText, $encrypted, $keyHex) === true ? 'OK' : 'FAILED');
echo PHP_EOL;

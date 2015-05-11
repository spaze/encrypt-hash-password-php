<?php
// Run with php tests/encrypthash.php
require_once './functions-encrypthash.php';

$key = openssl_random_pseudo_bytes(16);
$plainText = 'Haxx0r ipsum tera dereference rsa ascii foo bypass flush ip';


echo 'Test: ';
$encrypted = hashAndEncrypt($plainText, $key);
echo (decryptAndVerifyHash($plainText, $encrypted, $key) === true ? 'OK' : 'FAILED');
echo PHP_EOL;

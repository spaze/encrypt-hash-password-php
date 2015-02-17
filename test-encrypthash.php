<?php
require_once './functions-encrypthash.php';

$key = current(unpack('H64', mcrypt_create_iv(32, MCRYPT_DEV_RANDOM)));
$plainText = 'Haxx0r ipsum tera dereference rsa ascii foo bypass flush ip';
$encrypted = encryptAndHash($plainText, $key);

echo 'Test: ';
echo (decryptAndVerifyHash($plainText, $encrypted, $key) === true ? 'OK' : 'FAILED');
echo PHP_EOL;

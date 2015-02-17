<?php
// Run with php hash.php
$plainText = 'Haxx0r ipsum tera dereference rsa ascii foo bypass flush ip';
$hash = password_hash($plainText, PASSWORD_DEFAULT);

echo 'Test: ';
echo (password_verify($plainText, $hash) === true ? 'OK' : 'FAILED');
echo PHP_EOL;

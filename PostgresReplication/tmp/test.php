<?php
$f="test.arr";

$P = unserialize(base64_decode(file_get_contents($f)));

print_r($P);


?>


<?php

include "inc.php";

$msg= new Message($conn);

// Z�hler beim Auslesen zur�cksetzen ==> bei Kopierten Datenbank, oder ge�ndertem Bot notwendig
// $msg->get(true);

$msg->get(false);
$msg->show();
echo "\n\n";



?>


<?php

include "inc.php";

$msg= new Message($conn);

// Zähler beim Auslesen zurücksetzen ==> bei Kopierten Datenbank, oder geändertem Bot notwendig
// $msg->get(true);

$msg->get(false);
$msg->show();
echo "\n\n";



?>


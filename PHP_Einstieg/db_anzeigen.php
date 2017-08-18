<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=Cp1252">
<title>PHP&MySQL</title>
</head>
    <body>
    <p>Testabfragen:</p>
    <?php
$con=mysqli_connect("127.0.0.1","root","", "firma");

if (mysqli_connect_errno())
{
    echo "Failed to connect to MySQL: " . mysqli_connect_errno();
} else {
    echo " Connected Successfully!<br>";
}


$res= mysqli_query($con, "SELECT * FROM personen");

$num=mysqli_num_rows($res);



if ($num > 0) 
       echo "Ergebnis:<br>";
else 
    echo "Keine Ergebnisse<br>";



while ($dsatz = mysqli_fetch_assoc($res)){
    echo $dsatz["name"] . ", " 
    .    $dsatz["vorname"] . " , "
    .    $dsatz["personalnummer"] . " , "
    .    $dsatz["geburtstag"] . "<br>";
}

mysqli_close($con)
	?>
    </body>

</html>
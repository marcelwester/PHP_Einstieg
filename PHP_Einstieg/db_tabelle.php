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
//test

if (mysqli_connect_errno())
{
    echo "Failed to connect to MySQL: " . mysqli_connect_errno();
} else {
    echo " Connected Successfully!<br>";
}


$res= mysqli_query($con, "SELECT * FROM personen");


// Tabellen Beginn
echo "<table border='1'>";

// Überschrift

echo "<tr> <td> Lfd. Nr. </td> <td>Name</td>";
echo "<td>Vorname</td> <td>Personalnummer</td>";
echo "<td>Gehalt</td> <td> Geburtstag</td>";

$lf=1;

while ($dsatz = mysqli_fetch_assoc($res))
{
    echo "<tr>";
    echo "<td>$lf</td>";
    echo "<td>" . $dsatz["name"] . "</td>";
    echo "<td>" . $dsatz["vorname"] . "</td>";
    echo "<td>" . $dsatz["personalnummer"] . "</td>";
    echo "<td>" . $dsatz["gehalt"] . "</td>";
    echo "<td>" . $dsatz["geburtstag"] . "</td>";
    $lf= $lf + 1;
    
    
}


echo "</table>";

mysqli_close($con)
	?>
    </body>

</html>
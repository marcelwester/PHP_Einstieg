<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=Cp1252">
<title>Insert title here</title>
</head>
    <body>
    <p>This page uses frames. The current browser you are using does not support frames.</p>
    <?php
    
    
    $con=mysqli_connect("127.0.0.1","root","", "hardware");
    
    if (mysqli_connect_errno())
    {
        echo "Failed to connect to MySQL: " . mysqli_connect_errno();
    } else {
        echo " Connected Successfully!<br>";
    }
    mysqli_select_db($con, "hardware");
      
    
    $sql=    "SELECT * FROM fp";
   
    echo "Abfrage: " . $sql ."<br>";
    $res= mysqli_query($con, $sql);
    if (!$check1_res) {
        printf("Error: %s\n<br>", mysqli_error($con));        
    }

    $num=mysqli_num_rows($res);
    
    if($num > 0)  echo "Ergebnis:<br>";
    else          echo "Kein Ergebnis<br>";
    
    while ($dsatz = mysqli_fetch_assoc($res))
        echo $dsatz ["hersteller"] . " , " . $dsatz["typ"] . " , " . $dsatz["gb"]  . " , " .  $dsatz["preis"] . 
           " , " . $dsatz["artnummer"] .  "<br>";
    
        
        mysqli_close($con);
  

	?>
    </body>

</html>
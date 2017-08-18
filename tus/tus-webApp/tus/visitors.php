<?php
  	if (priv("visitors"))
	{
    	sitename("visitors.php",$_SESSION["groupid"]);
    	echo '<center><h2><u>Besucher-Statistik</u></h2></center>';
     	echo '<IMG SRC="visitorgraphic_month.php?action=tus" />';
     	echo '<br><br>';
     	echo '<IMG SRC="visitorgraphic_365.php?action=tus" />';
     	echo '<br><br>';
     	echo '<IMG SRC="visitorgraphic_month_average.php?action=tus" />';
    } else 
    	echo $no_rights;
?>
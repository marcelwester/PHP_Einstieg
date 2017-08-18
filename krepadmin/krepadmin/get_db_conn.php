<?php
	$url='index.php?menu=db_connection&PHPSESSID='.session_id();
	echo '<h2>Datenbankverbindung</h2>';

   if (!isset($_GET["action"])) {
   	$action="start";
   } else {
   	$action=$_GET["action"];
   }

   switch ($action) {
	case 'start':
		if (isset($db)) oclose($db);
  		unset($_SESSION["tns"]);
		unset($_SESSION["repadmin"]);
  		unset($_SESSION["repadminpwd"]);

      echo '<table align="center" width="50%" border="1">';
		echo '<form method="POST" action="'.$url.'&action=check">';

         echo '<tr>';
		      echo '<td align="center" width="50%">';
		   	   echo '<b>DB-Connection (TNS-Alias)</b>';
	   	   echo '</td>';
	 		   echo '<td align="center" width="50%">';
					echo '<input type="text" name="tns" value="" size="32" />';
	         echo '</td>';
	      echo '</tr>';

         echo '<tr>';
		      echo '<td align="center" width="50%">';
		   	   echo '<b>Replikationsadministrator:</b>';
	   	   echo '</td>';
	 		   echo '<td align="center" width="50%">';
					echo '<input type="text" name="repadmin" value="" size="32" />';
	         echo '</td>';
	      echo '</tr>';

	      echo '<tr>';
	         echo '<td align="center" width="50%">';
	            echo '<b>Passwort</b>';
	         echo '</td>';
	         echo '<td align="center" width="50%">';
					echo '<input type="password" name="repadminpwd" value="" size="32" />';
	         echo '</td>';
	      echo '</tr>';

	      echo '<tr>';
	         echo '<td align="center" colspan="2">';
	            echo '<input type="submit" value="Ok" />';
	         echo '</td>';
	      echo '</tr>';
		echo '</form>';
      echo '</table>';
   break;

	case 'check':
  		$_SESSION["tns"]=$_POST["tns"];
		$_SESSION["repadmin"]=strtoupper($_POST["repadmin"]);
  		$_SESSION["repadminpwd"]=$_POST["repadminpwd"];
		$tns=$_SESSION["tns"];
		$user=$_SESSION["repadmin"];
	   $pass=$_SESSION["repadminpwd"];
		include "db_ora.php";
	   include "ora_krep_inc.php";
      $sqlstr="select sysdate from dual";
		$db=oconnect();
      $result=getResult($db,$sqlstr);
      if (!isset($result)) {
      	echo "<br><h2>Fehler Datenbank Verbindung !</h2>";
	  		unset($_SESSION["tns"]);
			unset($_SESSION["repadmin"]);
  			unset($_SESSION["repadminpwd"]);

      } else {
			echo "<br><h2>Verbindung erfolgreich aufgebaut !</h2>";
      }
   break;
   }
?>
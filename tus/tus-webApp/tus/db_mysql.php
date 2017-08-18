<?php

/********************************
Datenbankanbindung f�r MySQL
Daniel Sager | 28.07.2003
********************************/


// Funktion um den Connect zur Datenbank auszuf�hren
function doConnect($host = null, $user = null, $passwd = null)
{
	// Fehlerbehandlung falls kein Host, Benutzer �bergeben
	if ($host == null || $user == null)
	{
		$return = array(code => 1,
				message1 => 'kein Host oder Benutzer �bergeben',
				message2 => ' ');
		return $return;
	}

	$db = mysql_connect($host,$user,$passwd);

	if (!$db)	// DB Connect fehlgeschlagen
	{
		$return = array(code => 1,
				message1 => 'konnte keine Verbindung zur Datenbank herstellen',
				message2 => mysql_error());
		return $return;
	}
	else		// DB Connect erfolgreich
	{
		$return = array(code => 0,
				db => $db);
		return $return;
	}
}


// Funktion um die Datenbankverbindung zu schliessen
function closeConnect($db)
{
	// Fehlerbehandlung falls keine MySQL Verbindungs-Kennung �bergeben
	if ($db == null)
	{
		$return = array(code => 1,
				message => 'keine MySQL Verbindungs-Kennung �bergeben',
				message2 => ' ');
		return $return;
	}

	$dummy = mysql_close($db);

	if (!$dummy)	// DB close fehlgeschlagen
	{
		$return = array(code => 1,
				message1 => 'konnte die gew�nschte Datenbankverbindung nicht schliessen',
				message2 => mysql_error());
		return $return;
	}
	else		// DB close erfolgreich
	{
		$return = array(code => 0);
		return $return;
	}
}


// Funktion um die Datenbank auszuw�hlen
function selectDB($db)
{
	// Fehlerbehandlung falls keine MySQL Verbindungs-Kennung �bergeben
	if ($db == null)
	{
		$return = array(code => 1,
				message => 'keine MySQL Verbindungs-Kennung �bergeben',
				message2 => ' ');
		return $return;
	}

	$dummy = mysql_select_db($db);

	if (!$dummy)	// DB Select fehlgeschlagen
	{
		$return = array(code => 1,
				message1 => 'konnte die gew�nschte Datenbank nicht ausw�hlen',
				message2 => mysql_error());
		return $return;
	}
	else		// DB Select erfolgreich
	{
		$return = array(code => 0);
		return $return;
	}
}


// Funktion um ein Select Statement abzusetzen und das Ergebnis zu formatieren
function getResult($db = null, $sql = null)
{
	global $DEBUG;
	// Fehlerbehandlung falls kein SQL Statement
	// oder keine MySQL Verbindungs-Kennung �bergeben
	if ($sql == null || $db == null)
	{
		$return = array(code => 1,
				message1 => 'kein SQL Statement oder keine DB-Kennung �bergeben',
				message2 => ' ');
		return $return;
	}

	$result = mysql_query($sql,$db);

	if (!$result)
	{
		$return = array(code => 1,
				message1 => 'Fehler bei der Ausf�hrung des SQL Statements',
				message2 => mysql_error());
				if ($DEBUG == "1") {
					echo '<SCRIPT LANGUAGE="JavaScript">';
					 echo 'alert("Fehler doSQL: '.$sql.'");';								
					echo '</SCRIPT>';
				}
		return $return;
	}

	$x = 0;
	while($datensaetze = mysql_fetch_array($result))
	{
		foreach($datensaetze as $key => $val)
			$return[$x][$key] = $val;
		$x++;
	}

	mysql_free_result($result);

	return $return;
}


// Funktion um ein SQL Statement abzusetzen gegebenenfalls eine Fehlermeldung zur�ck zugeben
function doSQL($db = null, $sql = null)
{
	global $SQLDEBUG,$DEBUG;
	// Fehlerbehandlung falls kein SQL Statement
	// oder keine MySQL Verbindungs-Kennung �bergeben
	if ($sql == null || $db == null)
	{
		$return = array(code => 1,
				message1 => 'kein SQL Statement oder keine DB-Kennung �bergeben',
				message2 => ' ');
		return $return;
	}

	$result = mysql_query($sql,$db);

	if (!$result)
	{
		$return = array(code => 1,
				message1 => 'Fehler bei der Ausf�hrung des SQL Statements',
				message2 => mysql_error());
				if ($DEBUG == "1") {
					echo '<SCRIPT LANGUAGE="JavaScript">';
					 echo 'alert("Fehler doSQL: '.$sql.'");';								
					echo '</SCRIPT>';
				}
		return $return;
	}

	$return = array(code => 0,
			message1 => 'Alles in Ordnung');

	if ($SQLDEBUG==1) {
		echo '<br>';
		print_r($return);
	}
	return $return;
}


?>

<?php
require_once 'sml_parser.php';

$pathname='/opt/htdocs/dev/sml/data/';

if ($handle = opendir($pathname)) {
	while (false !== ($file = readdir($handle))) {
		if ($file != "." && $file != "..") {
			$files[]=$file;
		}
	}

	if(is_array($files)) {
		sort($files);
	
		foreach($files as $file) {
			if(substr($file,0,9)!='serialin_') {
				echo $file;
				$sml_parser = new SML_PARSER();
				$sml_parser->parse_sml_file($pathname.$file);
				$values = $sml_parser->get_first_values();
				 
				
				
				print_r($values);
				 
				$time = date('Y-m-d H:i:s',filemtime($pathname.$file));

				$OBIS_1_8_1 = $values['0100010801FF']['value']*$values['0100010801FF']['scaler']/1000; # Wh -> kWh
				$public_key = $values['8181C78205FF']['value'];
				$active_power = $values['01000F0700FF']['value'];

				
				/*
				$mysqlhost="<host>";
				$mysqluser="<benutzer>";
				$mysqlpwd="<passwort>";
				$connection=mysql_connect($mysqlhost,   $mysqluser, $mysqlpwd) or   die ("Verbindungsversuch fehlgeschlagen");
				$mysqldb="<datenbank>";
				mysql_select_db($mysqldb,$connection) or    die("Konnte die Datenbank   nicht   waehlen.");
				 
				$sql = "INSERT INTO stromzaehler
				(timestamp,public_key,zaehlerstand,active_power)
				VALUES ('$time','$public_key','$OBIS_1_8_1','$active_power')";
				mysql_query($sql) or die($sql);
				 
				 
				//Trägt einen Snapshot der aktuellen Daten in eine extra Tabelle ein
				//damit der Raspi nicht immer durch die komplette Tabelle pflügen muss
				$sql = "UPDATE strom_snapshot SET zeitstempel=CURRENT_TIMESTAMP, zaehlerstand='$OBIS_1_8_1', wirkleistung='$active_power'";
				mysql_query($sql) or die($sql);
				 */
				echo "VALUES: ",PHP_EOL;
				echo $OBIS_1_8_1,PHP_EOL;
				echo $public_key,PHP_EOL;
				echo $active_power,PHP_EOL;
				
				//unlink($pathname.$file);
			}
		}
	}
	closedir($handle);
}
?>
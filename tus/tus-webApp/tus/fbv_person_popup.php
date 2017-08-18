<?php

////
//// fbv_person_popup.php
////
//// Änderung: Volker 25.03.2005
//// Zusätzliches Feld passnr eingefügt
////
//// Änderung: Volker 26.02.2004
//// Zusätzliches Feld Bemerkung eingefügt
////
////
//// letzte Änderung: Daniel 26.02.2004 18:56
//// Info wieder rausgenommen -> ma_kader_info_popup.php
////
//// Änderung: Volker 16.02.2004
//// Darstellung Vor- und Nachname bei INFO
////
//// Änderung : Daniel, 16.02.2004 14:55
//// was : Unterteilung Vorname/Name
////
//// Änderung : Volker 15.02.2004
//// was : Hinzufügen von case 'info' für anzeigen von Spielerinfo vom Kader aus
//// Änderung an viewImage: => viewImage(0) ; wird aber nicht genutzt
////
//// Änderung : Daniel, 10.02.2004 19:56
//// was : Ändern von Personen (Speichern geht noch nicht)
////
//// Änderung : Daniel, 10.02.2004 19:56
//// was : Speichern von neuen Personen
////
//// Änderung : Daniel, 28.01.2004 23:05
//// was : Datei erstellt

include "inc.php";
sitename("fbv_person_popup.php",$_SESSION["groupid"]);
if (priv("fb_person") || priv("kader"))
{

?>

<HTML>
<HEAD>
<?php
if ($_REQUEST["action"] == 'info')
  echo '<TITLE>w w w . t u s - o c h o l t . d e - Personen Information</TITLE>';
else
  echo '<TITLE>w w w . t u s - o c h o l t . d e - Personenverwaltung</TITLE>';
?>
</HEAD>
<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">

<SCRIPT LANGUAGE="JavaScript">
<!--
        function viewImage(imageid)
        {
           if (imageid==0)
              var imgId = document.getElementsByName("foto")[0].value;
           else
              imgId = imageid;

           if (imgId == 0)
                    alert ('Bitte ein Bild zum Anzeigen auswählen !');
            else
                    window.open("showimage.php?id="+imgId,"viewImage","width=650, height=550, top=200, left=200, menubar=no, directories=no, resizeable=yes, toolbar=no, scrollbars=yes, status=no");
        }
-->
</SCRIPT>

<?php
focus();
switch ($_REQUEST["action"])
{
        case 'add':
                echo '<FORM METHOD="POST" ACTION="fbv_person_popup.php?action=save&PHPSESSID='.session_id().'">';
                echo '<TABLE WIDTH="100%" BORDER="0">';
                        echo '<TR>';
                                echo '<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                                        echo '<B>Neue Person anlegen</B>';
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Vorname<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="50" NAME="vorname" VALUE="" />';
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Name<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="50" NAME="name" VALUE="" />';
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Geschlecht<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo 'männlich <input type="Radio" name="geschlecht" value="m" checked="checked">';
                                        echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                                        echo 'weiblich <input type="Radio" name="geschlecht" value="w">';
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Strasse<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="50" NAME="strasse" VALUE="" />';
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>PLZ, Ort<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="50" NAME="plzort" VALUE="" />';
                                echo '</TD>';
                        echo '</TR>';

                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Telefon<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="50" NAME="tel" VALUE="" />';
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Email<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="50" NAME="email" VALUE="" />';
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Geburtsdatum<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="12" NAME="bday" VALUE="" /> (bitte im Format TT.MM.JJJJ angeben !)';
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Mitglied seit<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="12" NAME="mitgld_dat" VALUE="" /> (bitte im Format TT.MM.JJJJ angeben !)';
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Pass-Nr.<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="50" maxlength="32" NAME="pass_nr" VALUE="" />';
                                echo '</TD>';
                        echo '</TR>';
   							echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Bemerkung<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="50" NAME="bemerkung_dat" VALUE="'.$person["bemerkung"].'" />';
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Foto<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        $sql_fotos = 'select * from sys_images where kategorie = 2 and linked = 0 order by descr';
                                        $result_fotos = getResult($db,$sql_fotos);
                                        echo '<select name="foto">';
                                        echo '<option value="0" selected>keins</option>';
                                        foreach($result_fotos as $foto)
                                                echo '<option value="'.$foto["image_id"].'">'.$foto["descr"].'</option>';
                                        echo '</select>';
                                        echo '&nbsp;<A HREF="javascript:viewImage(0);">';
                                        echo '<IMG SRC="images/view.jpg" HEIGHT="16" WIDTH="16" BORDER="0" ALT="Bild ansehen"></A>';
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF" COLSPAN="2">';
                                        //$_SESSION["edit_kader"] = 0;
                                        echo '<INPUT TYPE="button" VALUE="Abbrechen" onClick="window.close();">';
                                        echo '&nbsp;&nbsp;<INPUT TYPE="submit" VALUE="Anlegen">';
                                echo '</TD>';
                        echo '</TR>';
                echo '</TABLE>';
                echo '</FORM>';
                break;
        case 'edit':
                $sql_person = 'select * from fb_person where id = '.$_REQUEST["personid"];
                $result_person = getResult($db, $sql_person);
                $person = $result_person[0];

                echo '<FORM METHOD="POST" ACTION="fbv_person_popup.php?action=save&PHPSESSID='.session_id().'">';

                echo '<INPUT TYPE="HIDDEN" NAME="id" VALUE="'.$_REQUEST["personid"].'" />';

                echo '<TABLE WIDTH="100%" BORDER="0">';
                        echo '<TR>';
                                echo '<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                                        echo '<B>Person bearbeiten</B>';
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Vorname<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="50" NAME="vorname" VALUE="'.$person["vorname"].'" />';
                                echo '</TD>';
                        echo '</TR>';                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Name<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="50" NAME="name" VALUE="'.$person["name"].'" />';
                                echo '</TD>';
                        echo '</TR>';

                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Geschlecht<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        if ($person["geschlecht"]=="m") {
                                        	echo 'männlich <input type="Radio" name="geschlecht" value="m" checked="checked">';
                                        	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                                        	echo 'weiblich <input type="Radio" name="geschlecht" value="w">';
                                        } else {
                                        	echo 'männlich <input type="Radio" name="geschlecht" value="m">';
                                        	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                                        	echo 'weiblich <input type="Radio" name="geschlecht" value="w"  checked="checked">';
                                        }
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Strasse<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="50" NAME="strasse" VALUE="'.$person["strasse"].'" />';
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>PLZ, Ort<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="50" NAME="plzort" VALUE="'.$person["PLZ_Ort"].'" />';
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Telefon<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="50" NAME="tel" VALUE="'.$person["tel"].'" />';
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Email<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="50" NAME="email" VALUE="'.$person["email"].'" />';
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Geburtsdatum<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                	if (isset($person["geburtsdatum"]))
                                		$bday=date("d.m.Y", strtotime($person["geburtsdatum"]));
                                	else
                                		$bday="";

                                        echo '<INPUT TYPE="TEXT" SIZE="12" NAME="bday" VALUE="'.$bday.'" /> (bitte im Format TT.MM.JJJJ angeben !)';
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Mitglied seit<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="12" NAME="mitgld_dat" VALUE="'.date("d.m.Y", strtotime($person["mitglied_datum"])).'" /> (bitte im Format TT.MM.JJJJ angeben !)';
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Pass-Nr.<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="50" maxlength="32" NAME="pass_nr" VALUE="'.$person["passnr"].'" />';
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Bemerkung<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="50" NAME="bemerkung_dat" VALUE="'.$person["bemerkung"].'" />';
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Foto<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="HIDDEN" NAME="foto_old" VALUE="'.$person["foto_id"].'" />';
                                        $sql_fotos = 'select * from sys_images where kategorie = 2 order by descr';
                                        $result_fotos = getResult($db,$sql_fotos);
                                        echo '<select name="foto">';
                                        if ($person["foto_id"] == null)
                                                echo '<option selected value="0" >keins</option>';
                                        else
                                                echo '<option value="0" >keins</option>';
                                        foreach($result_fotos as $foto)
                                                if ($foto["image_id"] == $person["foto_id"] || $foto["linked"] == 0)
                                                        if ($foto["image_id"] == $person["foto_id"])
                                                                echo '<option selected value="'.$foto["image_id"].'">'.$foto["descr"].'</option>';
                                                        else
                                                                echo '<option value="'.$foto["image_id"].'">'.$foto["descr"].'</option>';
                                        echo '</select>';
                                        echo '&nbsp;<A HREF="javascript:viewImage(0);">';
                                        echo '<IMG SRC="images/view.jpg" HEIGHT="16" WIDTH="16" BORDER="0" ALT="Bild ansehen"></A>';
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF" COLSPAN="2">';
                                        //$_SESSION["edit_kader"] = 0;
                                        echo '<INPUT TYPE="button" VALUE="Abbrechen" onClick="window.close();">';
                                        echo '&nbsp;&nbsp;<INPUT TYPE="submit" VALUE="Ändern">';
                                echo '</TD>';
                        echo '</TR>';
                echo '</TABLE>';
                echo '</FORM>';
                break;
        case 'save':

                if (isset($_POST["id"]))
                        $id = $_POST["id"];
                if (isset($_POST["foto_old"]))
                        $foto_old = $_POST["foto_old"];

                if (strlen($_POST["name"]) != 0)
                {
                        $name = $_POST["name"];
                        $vorname = $_POST["vorname"];
                        $geschlecht = $_POST["geschlecht"];


                        if (strlen($_POST["strasse"]) != 0)
                                $strasse = $_POST["strasse"];
                        else
                                $strasse = 'NULL';

                        if (strlen($_POST["plzort"]) != 0)
                                $plzort = $_POST["plzort"];
                        else
                                $plzort = 'NULL';

                        if (strlen($_POST["tel"]) != 0)
                                $tel = $_POST["tel"];
                        else
                                $tel = 'NULL';

                        if (strlen($_POST["email"]) != 0)
                                $email = $_POST["email"];
                        else
                                $email = 'NULL';

                        if (strlen($_POST["bday"]) != 0)
                                $bday = ts2db($_POST["bday"]);
                        else
                                $bday = 'NULL';

                        if (strlen($_POST["mitgld_dat"]) != 0)
                                $mitglied = ts2db($_POST["mitgld_dat"]);
                        else
                                $mitglied = 'NULL';

                        if ($_POST["foto"] == 0)
                                $fotoid = 'NULL';
                        else
                                $fotoid = $_POST["foto"];

			if (strlen($_POST["bemerkung_dat"]) != 0)
                                $bemerkung = $_POST["bemerkung_dat"];
                        else
                                $bemerkung = 'NULL';


                        if (!isset($id))        // neu anlegen
                        {
                                if ($fotoid != 'NULL')
                                {
                                        $sql_foto = 'update sys_images set linked = 1 where image_id = '.$fotoid;
                                        $result2 = doSQL($db,$sql_foto);
                                }

                                $sql = 'insert into fb_person (vorname, name, email, tel, foto_id, mitglied_datum, geburtsdatum,bemerkung,passnr,strasse,PLZ_Ort,geschlecht) ';
                                $sql .= 'values ("'.$vorname.'", "'.$name.'", "'.$email.'", "'.$tel.'", '.$fotoid.', "'.$mitglied.'", "'.$bday.'", "'.$bemerkung.'", "'.$_POST["pass_nr" ].'","'.$strasse.'","'.$plzort.'","'.$geschlecht.'" )';

                                $sql = ereg_replace('"NULL"', 'NULL', $sql);
                                $result1 = doSQL($db,$sql);
                        }
                        else                        // alten updaten
                        {
                                if ($fotoid != $foto_old)        // Foto geändert
                                {
                                        $sql_foto = 'update sys_images set linked = 1 where image_id = '.$fotoid;
                                        $result2 = doSQL($db,$sql_foto);
                                        if ($foto_old != "")
                                        {
                                        	$sql_foto = 'update sys_images set linked = 0 where image_id = '.$foto_old;
                                        	$result2 = doSQL($db,$sql_foto);
                                        }
                                }
                                $sql = 'update fb_person set ';
                                $sql .= 'vorname = "'.$vorname.'", ';
                                $sql .= 'name = "'.$name.'", ';
                                $sql .= 'strasse = "'.$strasse.'", ';
                                $sql .= 'PLZ_Ort = "'.$plzort.'", ';                                
                                $sql .= 'email = "'.$email.'", ';
                                $sql .= 'tel = "'.$tel.'", ';
                                $sql .= 'passnr = "'.$_POST["pass_nr"].'", ';
                                $sql .= 'foto_id = "'.$fotoid.'", ';
                                $sql .= 'mitglied_datum = "'.$mitglied.'", ';
                                if (strlen($_POST["bday"]) != 0) {
	                                $sql .= 'geburtsdatum = "'.$bday.'", ';
                                } else {
                                     $sql .= 'geburtsdatum = "NULL", ';
                                }
                                $sql .= 'bemerkung = "'.$bemerkung.'", ';
                                $sql .= 'geschlecht = "'.$geschlecht.'" ';
                                $sql .= 'where id = '.$id;

                                $sql = ereg_replace('"NULL"', 'NULL', $sql);
                                $result1 = doSQL($db,$sql);
                        }

                        if ($result1["code"] == 0 && $result2["code"] == 0)
                        {
                        	mlog("Fussballverwaltung: Speichern eines Spielers: ".$id);
                        	//$_SESSION["edit_kader"] = 0;
                                 echo '<CENTER><BR><BR><BR>Die Aktion wurde erfolgreich ausgeführt.<br><A HREF="javascript:opener.location.reload(); window.close();">Hier klicken, um das Fenster zu schließen</A></CENTER>';
                                 echo '<SCRIPT TYPE="text/javascript">';
                                	echo 'opener.location.reload();';
                                 	echo 'setTimeout("window.close()",1000);';
                                 echo '</SCRIPT>';
                                //echo '<CENTER><BR><BR><BR>Die Aktion wurde erfolgreich ausgeführt.<br><A HREF="javascript:opener.location.reload(); window.close();">Hier klicken, um das Fenster zu schließen</A></CENTER>';
                        }
                        else
                        {
                        	//$_SESSION["edit_kader"] = 0;
                                echo '<CENTER><BR><BR><BR>Die Aktion konnte nicht erfolgreich ausgeführt werden.<br><A HREF="javascript:opener.location.reload(); window.close();">Hier klicken, um das Fenster zu schließen</A></CENTER>';
                        }
                }
                else
                	echo '<CENTER><BR><BR><BR>Bitte überprüfen Sie nochmal Ihre Eingaben.<br><A HREF="javascript:window.history.back();">Hier klicken, um zurück zu gehen</A></CENTER>';
                break;
	}
}
else
	echo 'Ihnen fehlt die Berechtigung, diese Seite anzuzeigen. Bitte wenden Sie sich an einen Administrator!';
?>
</BODY>
</HTML>
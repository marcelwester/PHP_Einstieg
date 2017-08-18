<?php
//// tt_ma_kader_print_popup.php
////
//// letzte Änderung : Volker, 22.07.2004
//// was : Erstellung
////

include "inc.php";
?>

<HTML>
<HEAD>
<TITLE>w w w . t u s - o c h o l t . d e</TITLE>
</HEAD>
<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">

<?php
          sitename("tt_ma_kader_print_popup.php",$_SESSION["groupid"]);
if (priv("kader") || priv("kader_print")) {
        echo '<input type="image" title="Drucken" src="./images/Printer.gif" onClick="print();">';
        echo' <br>'; 
	$teamid = $_REQUEST["teamid"];
	$saisonid = $_REQUEST["saisonid"];
	
	$sqlstr="select spielzeit,liga from tt_saison where id=".$saisonid;
	$result=GetResult($db,$sqlstr);
	
	$sqlstr="select name from tt_tus_mannschaft where id=".$teamid;
	$result1=GetResult($db,$sqlstr);
		
		
	echo '<div ALIGN="CENTER">';
	echo '<font size="+1">';
	echo $result1["0"]["name"].'<br>';
	echo $result["0"]["spielzeit"].'  '.$result["0"]["liga"];
	echo '</font><br>';
	echo 'Stand: '.date("d.m.Y");
	echo '</div>';
	echo '<br><br>';
	
	

        echo '<CENTER><TABLE WIDTH="100%" BORDER="0">';
                echo '<TR>';
                        echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                                echo '<B>Name</B>';
                        echo '</TD>';
                        echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                                echo '<B>Geb.</B>';
                        echo '</TD>';
                        echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                                echo '<B>Telefon</B>';
                        echo '</TD>';
                        echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                                echo '<B>email</B>';
                        echo '</TD>';

                echo '</TR>';
                        $sql_typ = 'select * from tt_person_typ order by id asc';
                        $result_typ = getResult($db, $sql_typ);
                        foreach ($result_typ as $typ)
                        {
                                echo '<TR><TD ALIGN="LEFT" COLSPAN="4" BGCOLOR="#FFFFFF"><B>'.$typ["name"].'</B></TD></TR>';
                                $sql_pers = 'select pe.name,pe.vorname,pe.mtel,zw.person_id,
                                             pe.email,pe.tel,pe.geburtsdatum
                                               from tt_zwtab_person_typ_tus_mannschaft zw,tt_person pe
                                               where person_id > 0 and zw.person_id=pe.id
                                               and aktiv = 1 and tus_mannschaft_id = '.$teamid.' and persontyp_id = '.$typ["id"].'
                                               and saison_id='.$saisonid.' order by pe.name';

                                $result_pers = getResult($db, $sql_pers);
                                if (isset($result_pers[0]))
                                        foreach ($result_pers as $pers)
                                        {
                                               echo '<TR>';
                                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                                        echo $pers["name"].', '.$pers["vorname"];
                                                echo '</TD>';
                                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                                        if (! isset($pers["geburtsdatum"]))
                                                        	echo '-';
                                                        else
                                                        	echo date("d.m.Y", strtotime($pers["geburtsdatum"]));
                                                echo '</TD>';
                                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	                                               echo $pers["tel"]."&nbsp;&nbsp;".$pers["mtel"];
                                                echo '</TD>';
                                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                                                if (! isset($pers["email"]))
                                                        	echo '-';
                                                        else
	                                                echo $pers["email"];
                                                echo '</TD>';


                                        echo '</TR>';
                                        }
                        }
        echo '</TABLE></CENTER>';

        echo '<SCRIPT TYPE="text/javascript">';
                echo 'setTimeout("window.print()",100);';
        echo '</SCRIPT>';

} else {
	echo $no_rights;
}
	  
	  
	
 
closeConnect($db);
?>
</BODY>
</HTML>
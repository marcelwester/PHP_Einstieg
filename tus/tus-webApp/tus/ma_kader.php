<?php


//// ma_kader.php
//// letzte Änderung: Volker 23.07.2004
//// Kader ausdrucken
//// 
//
//// letzte Änderung: Volker 16.02.2004
//// Darstellung Vor- und Nachname u. SQL Statement für Darstellung als Kreuzprodukt
//// und Sortierung nach Name
//
//// ma_kader.php
//// letzte Änderung: Volker 15.02.2004 14:45
//// Berücksichtiugung der Saisonid beim Lesen aus fb_zwtab_person_typ ....
//// Aktionsspalte auskommentiert
////Änderung für Aufruf von spieler.php
////
//// Änderung : Daniel, 11.02.2004 21:36
//// was : - Nur Spieler mit ID > 0 berücksichtigen
////
//// Änderung : Daniel, 07.02.2004 13:24
//// was : - Ausgabe der Spieler begonnen
////
?>

<SCRIPT LANGUAGE="JavaScript">
<!--
        function info(spielerid,saisonid)
        {
                var url;
        <?php
                echo 'url = "ma_kader_info_popup.php?action=info&personid="+spielerid+"&saisonid="+saisonid+"&PHPSESSID='.session_id().'";';
        ?>
                window.open(url,"info","width=450, height=500, top=200, left=200, menubar=no, directories=no, resizeable=yes, toolbar=no, scrollbars=yes, status=no");
        }
        
       	function edit(personid)
	{
		var url;
		if (personid == 0)
<?php
		
			echo 'url = "fbv_person_popup.php?action=add&PHPSESSID='.session_id().'";';
		echo ' else ';
			echo 'url = "fbv_person_popup.php?action=edit&personid="+personid+"&PHPSESSID='.session_id().'";';
?>
		window.open(url,"spieler","width=650, height=470, top=150, left=100, menubar=no, directories=no, resizeable=yes, toolbar=no, scrollbars=yes, status=no");
	}


       	function print_popup(saisonid,teamid)
	{
		var url;
<?php
		
			echo 'url = "ma_kader_print_popup.php?saisonid="+saisonid+"&teamid="+teamid+"&PHPSESSID='.session_id().'";';
?>
		window.open(url,"kaderdrucken","width=650, height=350, top=200, left=200, menubar=no, directories=no, resizeable=yes, toolbar=no, scrollbars=yes, status=no");
		//window.open(url,"kaderdrucken");
	}




        
<?php
if (priv("kader") && priv_team($teamid))
{
?>
        function popup(spielerid)
        {
              var url;
              <?php
                echo 'url = "ma_spieler.php?action=start&teamid='.$teamid.'&saisonid='.$saisonid.'&PHPSESSID='.session_id().'";';
              ?>
              window.open(url,"spieler","width=650, height=600, top=100, left=100, menubar=no, directories=no, resizeable=yes, toolbar=no, scrollbars=yes, status=no");
        }

    function enableDelete(chk_id, del_id, emp_id)
    {
            var elementChk = document.getElementById(chk_id);
            var elementDel = document.getElementById(del_id);
            var elementEmp = document.getElementById(emp_id);

            if (elementChk.checked == true)
            {
                    elementEmp.style.display = 'none';
                    elementDel.style.display = 'block';
            }
            else
            {
                    elementDel.style.display = 'none';
                    elementEmp.style.display = 'block';
            }
    }
<?php
}
?>
-->
</SCRIPT>
<?php
sitename("ma_kader.php",$_SESSION["groupid"]);
        echo '<CENTER><TABLE WIDTH="40%" BORDER="0">';
        if (priv("kader") && priv_team($teamid))
	{
        	$show_edit="1";
          
                        echo '<TR>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<a href="javascript:popup(0);">';
                                        echo '<IMG SRC="images/new.jpg" BORDER="0" ALT="neu">';
                                        echo '</a>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<a href="javascript:popup(0);">';
                                        echo '<B>Kader editieren</B>';
                                        echo '</a>';
                                echo '</TD>';
                        echo '</TR>';

        }
        
        if ((priv("kader") && priv_team($teamid)) ||priv("kader_print"))
        {
                       echo '<TR>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<a href="javascript:print_popup('.$_REQUEST["saisonid"].','.$_REQUEST["teamid"].');">';
                                        echo '<IMG SRC="images/Printer.gif" BORDER="0" ALT="drucken">';
                                        echo '</a>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<a href="javascript:print_popup('.$_REQUEST["saisonid"].','.$_REQUEST["teamid"].');">';
                                        echo '<B>Kader drucken</B>';
                                        echo '</a>';
                                echo '</TD>';
                        echo '</TR>';
        }
	echo '</TABLE></CENTER><BR>';
	
        echo '<CENTER><TABLE WIDTH="70%" BORDER="0">';
                echo '<TR>';
                        echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                                echo '<B>Name</B>';
                        echo '</TD>';
                        echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                                echo '<B>Info</B>';
                        if ($show_edit=="1") 
                        {
                        	echo '<B> / Edit</B>';
                        	
                        }
                        echo '</TD>';
                echo '</TR>';
                        $sql_typ = 'select * from fb_person_typ order by id asc';
                        $result_typ = getResult($db, $sql_typ);
                        foreach ($result_typ as $typ)
                        {
                                echo '<TR><TD ALIGN="LEFT" COLSPAN="'.$colspan_typ.'" BGCOLOR="#FFFFFF"><B>'.$typ["name"].'</B></TD></TR>';
                                $sql_pers = 'select pe.name,pe.vorname,zw.person_id
                                               from fb_zwtab_person_typ_tus_mannschaft zw,fb_person pe
                                               where person_id > 0 and zw.person_id=pe.id
                                               and aktiv = 1 and tus_mannschaft_id = '.$teamid.' and persontyp_id = '.$typ["id"].'
                                               and saison_id='.$saisonid.' order by pe.name';

                                $result_pers = getResult($db, $sql_pers);
                                if (isset($result_pers[0]))
                                        foreach ($result_pers as $pers)
                                        {
                                               echo '<TR>';
                                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                                        echo $pers["vorname"].' '.$pers["name"];
                                                echo '</TD>';
                                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                                  echo '<a href="javascript:info('.$pers["person_id"].','.$saisonid.');">';
                                                  echo '<IMG SRC="images/info.gif" BORDER="0" ALT="Info">';
                                                  echo '</a>';
                                                
                                                if ($show_edit=="1")
                                                {
                                                   echo "&nbsp;&nbsp;";
                                                   $_SESSION["edit_kader"]=1;
                                                   echo '<a href="javascript:edit('.$pers["person_id"].');">';
                                                   echo '<IMG SRC="images/edit.jpg" BORDER="0" ALT="Bearbeiten">';
                                                   echo '</a>';
                                                   
                                                }
						echo '</TD>';
                                        echo '</TR>';
                                        }
                        }
        echo '</TABLE></CENTER>';
?>
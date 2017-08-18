<?php

////
//// letzte ƒnderung : Daniel, 23.02.2004
//// was : Auswahl der Saison mit dem zuletzt gespielten Spiel
////

 include "inc.php";
 //sitename("index.php",$_SESSION["groupid"]);
 $_SESSION["edit_kader"] = 0;
        if (isset($_REQUEST["site"]))
                $site = $_REQUEST["site"];
        else
        {
                $site = "news";
	}
        if (isset($_REQUEST["action"]))
                $action = $_REQUEST["action"];
        else
        {
                $action = "currentnews";
		}
        if (!isset($_SESSION["groupid"]))
                $_SESSION["groupid"] = 0;

/*
$sql = 'select count(*) from sys_counter where date = now() and ip = "'.$_SERVER[REMOTE_ADDR].'"';
$result = getResult($db, $sql);

if ($result[0]["count(*)"] == 0)
{
	$sql = 'insert into sys_counter (ip, date) values ("'.$_SERVER[REMOTE_ADDR].'", now())';
	doSQL($db, $sql);
}
*/

// New Counter
if (!isset($_SESSION["count"])) {
	sys_counter();
	$_SESSION["count"]=1;
	 
}
?>

<HTML>
<HEAD>
<TITLE>w w w . t u s - o c h o l t . d e</TITLE>
  <meta http-equiv="content-type"     content="text/html; charset=iso-8859-1">
  <meta http-equiv="content-language" content="de">
  <meta http-equiv="expires"          content="0">


<SCRIPT LANGUAGE="JavaScript">
<!--
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
-->
</SCRIPT>

</HEAD>
<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">
<LINK REL="SHORTCUT ICON" HREF="images/favicon.ico">
<LINK REL="icon" HREF="images/favicon.ico">

<TABLE WIDTH="100%" BORDER="0" HEIGTH="100%">
        <TR>
                <TD ALIGN="CENTER" WIDTH="320" BGCOLOR="#FFFFFF">
                        <IMG SRC="images/tus_kl.jpg" BORDER="0" ALT="TuS Ocholt">
                </TD>
                <TD WIDTH="100%" BGCOLOR="#000000">
                        <CENTER><B><FONT FACE="Times New Roman" SIZE="7" COLOR="#FFFFFF">TuS Ocholt</FONT></B><br>
                        <EM><FONT FACE="Arial" SIZE="6" COLOR="#FFFFFF">"Mehr als Fuﬂball !!!"</FONT></EM>
                        </CENTER><br>
                </TD>
        </TR>
        <TR>
                <TD ALIGN="CENTER" BGCOLOR="#AAAAAA"><B>www.tus-ocholt.de</B></TD>
                <TD ALIGN="LEFT" BGCOLOR="#AAAAAA">
<?php
                         echo '<B>'.$contentHeaders[$site][$action].'</B>';
?>
                </TD>
        </TR>
        <TR>
                <TD WIDTH="20%" VALIGN="TOP">
                        <BR><BR>
						<?php
							include 'menu.php';
						?>
                        <BR><BR>
                        &nbsp;
                </TD>
                <TD VALIGN="TOP" ALIGN="CENTER" BGCOLOR="#FFFFFF">
                        <BR><BR>
<?php
                        include $contentHeaders[$site]["file"];
                 		closeConnect($db);
?>
                        <BR><BR>
                        &nbsp;
                </TD>
</TABLE>

</BODY>
</HTML>
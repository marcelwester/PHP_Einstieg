<?php

////
//// artikel_popup.php
////
//// letzte Änderung : Volker, 31.03.2004 
//// was : Erstellung
////

include "inc.php";
sitename("show_artikel.php",$_SESSION["groupid"]);
?>

<HTML>
<HEAD>
<TITLE>w w w . t u s - o c h o l t . d e - Artikeleditor</TITLE>
</HEAD>
<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">

<?php
		$artikelid=$_REQUEST["artikelid"];
		$sql = 'select content,id,name,images,descr,show_artikel,toc from sys_artikel where id='.$artikelid;
		$result = getResult($db, $sql);
		if (isset ($result))
		{
			echo image_replace($result["0"]["content"]);
		}

closeConnect($db);
?>
</BODY>
</HTML>
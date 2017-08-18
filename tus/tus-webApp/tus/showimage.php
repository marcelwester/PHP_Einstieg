<?php
////
//// showimage.php
////
//// Änderung: Daniel 18.03.2004
//// Erstellt

?>

<HTML>
<HEAD>
<TITLE>w w w . t u s - o c h o l t . d e - Bildbetrachtung</TITLE>
</HEAD>
<BODY>

<?php
    if (isset($_GET["nostats"]))     
    	echo '<IMG SRC="showimage2.php?nostats=1&id='.$_REQUEST["id"].'" />';
    else 
    	echo '<IMG SRC="showimage2.php?id='.$_REQUEST["id"].'" />';
?>

</BODY>
</HTML>
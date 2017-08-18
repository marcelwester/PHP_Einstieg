<?php
//// image_popup.php
////
//// letzte Änderung: Volker 18.02.2004
//// was: Erstellung
//// Zu jedem kleinen Logo kann ein Grosses Logo gelinked werden. 
//// In der Tabelle fb_Sponsoren sind lediglich zusätzlich Informationen zur image_id
//// in der Tabelle sys_images abgelegt. Es besteht eine 1:1 Beziehung zwischen sys_images und fb_sponsoren

include "inc.php";
sitename("sponsor_popup.php",$_SESSION["groupid"]);
?>

<HTML>
<HEAD>
<TITLE>w w w . t u s - o c h o l t . d e - Sponsoren Visitenkarte</TITLE>
</HEAD>
<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">

<?php

if (!priv("sponsoren")) {
	closeConnect($db);
	echo 'Ihnen fehlt die Berechtigung, diese Seite anzuzeigen. Bitte wenden Sie sich an einen Administrator!';
	exit;
}  

  $sponsorid = $_REQUEST["sponsorid"];

  switch ($_REQUEST["action"])
  {
     case 'add';
     	      echo '<FORM METHOD="POST" ACTION="sponsor_popup.php?action=save&PHPSESSID='.session_id().'">';
                echo '<TABLE WIDTH="100%" BORDER="0">';
                        echo '<TR>';
                                echo '<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                                        echo '<B>Sponsoren Visitenkarte</B><br>';
                                echo '</TD>';
                        echo '</TR>';
                        
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Name<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="50" NAME="name" VALUE="" />';
                                echo '</TD>';
                        echo '</TR>';
                        
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Firma<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="50" NAME="firma" VALUE="" />';
                                echo '</TD>';
                        echo '</TR>';
                        
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Strasse<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="50" NAME="strasse" VALUE="" />';
                                echo '</TD>';
                        echo '</TR>';
                        
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Ort<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="50" NAME="ort" VALUE="" />';
                                echo '</TD>';
                        echo '</TR>';
                        
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Telefon<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="50" NAME="telefon" VALUE="" />';
                                echo '</TD>';
                        echo '</TR>';
                        
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Internet Homepage<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="50" NAME="www" VALUE="" />';
                                echo '</TD>';
                        echo '</TR>';
                  
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Soll auf die Homepage verwiesen werden ?<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					//ja_nein
					$quest["0"]["name"] = "NEIN"; $quest["0"]["id"] = "0"; $quest["1"]["name"] = "JA"; $quest["1"]["id"] = "1";
					build_select($quest,name,id,www_link,"",1,"0");
                                        //echo '<INPUT TYPE="TEXT" SIZE="50" NAME="www_link" VALUE="'.$result["0"]["www_link"].'" />';
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Gültig ab<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="12" NAME="start" VALUE="'.date("d.m.Y").'" />';
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Gültig bis<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="12" NAME="end" VALUE="'.date("d.m.Y").'" />';
                                echo '</TD>';
                        echo '</TR>';

                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Kleines Logo <B>';
                                echo '</TD>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                   $r1["-1"]["image_id"]="0";
                                   $r1["-1"]["descr"]="nicht vorhanden";
                                   $sqlstr2 = "select image_id,descr from sys_images where (kategorie=4 and linked=0) order by descr";
                                   $r2 = getResult($db,$sqlstr2);
                                   $r = array_merge($r1,$r2);
                                   
                                   build_select($r,"descr","image_id","image_kl","","1","-1");     
                                        //echo '<INPUT TYPE="TEXT" SIZE="12" NAME="image_gr" VALUE="'.$result["0"]["image_gr"].'" />';
                                echo '</TD>';
                        echo '</TR>';

                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Grosses Logo <B>';
                                echo '</TD>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                   $r1["-1"]["image_id"]="0";
                                   $r1["-1"]["descr"]="nicht vorhanden";
                                   $sqlstr2 = "select image_id,descr from sys_images where (kategorie=5 and linked=0) order by descr";
                                   $r2 = getResult($db,$sqlstr2);
                                   $r = array_merge($r1,$r2);
                                   
                                   build_select($r,"descr","image_id","image_gr","","1","-1");     
                                        //echo '<INPUT TYPE="TEXT" SIZE="12" NAME="image_gr" VALUE="'.$result["0"]["image_gr"].'" />';
                                echo '</TD>';
                        echo '</TR>';

                        echo '<TR>';
                             echo '<TD COLSPAN=2 ALIGN="CENTER">';
                                  echo '<IMG SRC="showimage2.php?id='.$result["0"]["image_kl"].'" >';
                             echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF" COLSPAN="2">';
                                        echo '<INPUT TYPE="button" VALUE="Abbrechen" onClick="window.close();">';
                                        echo '&nbsp;&nbsp;<INPUT TYPE="submit" VALUE="Speichern">';
                                echo '</TD>';
                        echo '</TR>';
                echo '</TABLE>';
                echo '</FORM>';
      
     break;
     	
     
     
     case 'edit':

//	   print_r($_GET);	
//       $sqlstr = "select descr from sys_images where image_id=$imageid";
//       $result = getResult($db,$sqlstr);
//       $descr = $result["0"]["descr"]; 	   

	   // Daten lesen
	   $sqlstr = "select image_kl,image_gr,name,firma,strasse,ort,telefon,www,www_link,gueltig_ab,gueltig_bis 
        	      from fb_sponsoren where id = $sponsorid";
   	   $result = getResult($db,$sqlstr);
 
       $descr=$result["0"]["name"]." ".$result["0"]["firma"];
 
 	   $old_image_kl=$result["0"]["image_kl"];
 	   $old_image_gr=$result["0"]["image_gr"];	
          
     
             echo '<FORM METHOD="POST" ACTION="sponsor_popup.php?action=save&PHPSESSID='.session_id().'">';
                echo '<TABLE WIDTH="100%" BORDER="0">';
                        echo '<TR>';
                                echo '<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                                        echo '<B>Sponsoren Visitenkarte</B><br>'.$descr;
                                echo '</TD>';
                        echo '</TR>';
                        
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Name<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="50" NAME="name" VALUE="'.$result["0"]["name"].'" />';
                                echo '</TD>';
                        echo '</TR>';
                        
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Firma<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="50" NAME="firma" VALUE="'.$result["0"]["firma"].'" />';
                                echo '</TD>';
                        echo '</TR>';
                        
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Strasse<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="50" NAME="strasse" VALUE="'.$result["0"]["strasse"].'" />';
                                echo '</TD>';
                        echo '</TR>';
                        
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Ort<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="50" NAME="ort" VALUE="'.$result["0"]["ort"].'" />';
                                echo '</TD>';
                        echo '</TR>';
                        
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Telefon<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="50" NAME="telefon" VALUE="'.$result["0"]["telefon"].'" />';
                                echo '</TD>';
                        echo '</TR>';
                        
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Internet Homepage<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="50" NAME="www" VALUE="'.$result["0"]["www"].'" />';
                                echo '</TD>';
                        echo '</TR>';
                  
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Soll auf die Homepage verwiesen werden ?<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					//ja_nein
					$quest["0"]["name"] = "NEIN"; $quest["0"]["id"] = "0"; $quest["1"]["name"] = "JA"; $quest["1"]["id"] = "1";
					build_select($quest,name,id,www_link,"",1,$result["0"]["www_link"]);
                                        //echo '<INPUT TYPE="TEXT" SIZE="50" NAME="www_link" VALUE="'.$result["0"]["www_link"].'" />';
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Gültig ab<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="12" NAME="start" VALUE="'.date("d.m.Y", strtotime($result["0"]["gueltig_ab"])).'" />';
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Gültig bis<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="12" NAME="end" VALUE="'.date("d.m.Y", strtotime($result["0"]["gueltig_bis"])).'" />';
                                echo '</TD>';
                        echo '</TR>';

                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Kleines Logo <B>';
                                echo '</TD>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                   $r1["-1"]["image_id"]="0";
                                   $r1["-1"]["descr"]="nicht vorhanden";
                                   $sqlstr2 = "select image_id,descr from sys_images where (kategorie=4 and linked=0) or image_id=".$result["0"]["image_kl"]." order by descr";
                                   $r2 = getResult($db,$sqlstr2);
                                   $r = array_merge($r1,$r2);
                                   
                                   build_select($r,"descr","image_id","image_kl","","1",$result["0"]["image_kl"]);     
                                        //echo '<INPUT TYPE="TEXT" SIZE="12" NAME="image_gr" VALUE="'.$result["0"]["image_gr"].'" />';
                                echo '</TD>';
                        echo '</TR>';

                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Grosses Logo <B>';
                                echo '</TD>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                   $r1["-1"]["image_id"]="0";
                                   $r1["-1"]["descr"]="nicht vorhanden";
                                   $sqlstr2 = "select image_id,descr from sys_images where (kategorie=5 and linked=0) or image_id=".$result["0"]["image_gr"]." order by descr";
                                   $r2 = getResult($db,$sqlstr2);
                                   $r = array_merge($r1,$r2);
                                   
                                   build_select($r,"descr","image_id","image_gr","","1",$result["0"]["image_gr"]);     
                                        //echo '<INPUT TYPE="TEXT" SIZE="12" NAME="image_gr" VALUE="'.$result["0"]["image_gr"].'" />';
                                echo '</TD>';
                        echo '</TR>';

                        echo '<TR>';
                             echo '<TD COLSPAN=2 ALIGN="CENTER">';
                                  echo '<IMG SRC="showimage2.php?id='.$result["0"]["image_kl"].'" >';
                             echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF" COLSPAN="2">';
                                        echo '<INPUT TYPE="hidden" NAME="id" VALUE="'.$sponsorid.'">';
                                        echo '<INPUT TYPE="hidden" NAME="old_image_kl" VALUE="'.$old_image_kl.'">';
                                        echo '<INPUT TYPE="hidden" NAME="old_image_gr" VALUE="'.$old_image_gr.'">';
                                        echo '<INPUT TYPE="button" VALUE="Abbrechen" onClick="window.close();">';
                                        echo '&nbsp;&nbsp;<INPUT TYPE="submit" VALUE="Speichern">';
                                echo '</TD>';
                        echo '</TR>';
                echo '</TABLE>';
                echo '</FORM>';
      
     break;
     
     case 'save':
        if (isset($_REQUEST["id"]))
        	$id=$_REQUEST["id"];
        $name=$_REQUEST["name"];
        $firma=$_REQUEST["firma"];
        $strasse=$_REQUEST["strasse"];
        $ort=$_REQUEST["ort"];
        $www=$_REQUEST["www"];
        $www_link=$_REQUEST["www_link"];
        $start=$_REQUEST["start"];
        $end=$_REQUEST["end"];
        $telefon=$_REQUEST["telefon"];
        $image_gr=$_REQUEST["image_gr"];
        $image_kl=$_REQUEST["image_kl"];
        $old_image_gr=$_REQUEST["old_image_gr"];
        $old_image_kl=$_REQUEST["old_image_kl"];

        
        $gueltig_ab = ts2db($start);
        $gueltig_bis = ts2db($end);
        
        
        
      if (($gueltig_ab == "-1") ||($gueltig_bis == "-1"))
	{         
         echo "<br>Fehler im Datumsformat<br>";
         $result["code"]="1";
      	}
      else
      {
        
      	if (isset($id))
      		$sqlstr = "update fb_sponsoren set 
      			name='$name',
			firma='$firma',
      			strasse='$strasse',
      			ort='$ort',
      			telefon='$telefon',
      			image_kl=$image_kl,
      			image_gr=$image_gr,
      			gueltig_ab='$gueltig_ab',
      			gueltig_bis='$gueltig_bis',
      			www_link=$www_link,
      			www='$www'
      			where id = $id";
      	else
      	{
      		
      		   	$sqlstr = "insert into fb_sponsoren (name,firma,strasse,ort,telefon,image_kl,image_gr,
      	   	        gueltig_ab,gueltig_bis,www_link,www) values (
      	   	        '$name','$firma','$strasse','$ort','$telefon',$image_kl,$image_gr,'$gueltig_ab','$gueltig_bis',
      			$www_link,'$www')";
      	}
      	
      	$result = doSQL($db,$sqlstr);
      	//echo $sqlstr;
        if ($result["code"] == 0 )
        {
          
        
          // Linked Feld für grosse Logos setzen
          //echo $old_image_gr." -  ".$image_gr;
          if ($old_image_gr != $image_gr)
          {
             	if (isset($old_image_gr))
             	{
                       $result1=doSQL($db,"update sys_images set linked=0 where image_id=$old_image_gr");
             	}
               	$result1=doSQL($db,"update sys_images set linked=1 where image_id=$image_gr");
       	  }
       	  
          // Linked Feld für kleine Logos setzen
          if ($old_image_kl != $image_kl)
          {
             	if (isset($old_image_kl))
             		$result1=doSQL($db,"update sys_images set linked=0 where image_id=$old_image_kl");
             	$result1=doSQL($db,"update sys_images set linked=1 where image_id=$image_kl");
       	  }
       	  
         }
        }
          
        
      // $result["code"]=1  ;
      if ($result["code"] == 0)
      {
           mlog("Ein Sponsore wurde gespeichert: ".$id);
           echo 'Die Aktion wurde erfolgreich ausgeführt.<br><A HREF="javascript:opener.location.reload(); window.close();">Hier klicken, um das Fenster zu schließen</A>';
           echo '<SCRIPT TYPE="text/javascript">';
                echo 'opener.location.reload();';
                echo 'setTimeout("window.close()",1000);';
           echo '</SCRIPT>';
      }

      else
           echo 'Die Aktion konnte nicht erfolgreich ausgeführt werden.<br><A HREF="javascript:window.history.back();">Hier klicken, um zurückzukehren</A>';
     
     break;
  } 

closeConnect($db);
?>
</BODY>
</HTML>
<?php
//// sponsoren.php
////
//// Änderung : Volker, 21.02.2004 
//// was : - Darstellung der Sponsoren in Abhängigkeit der Gültigkeit
////



?>




<SCRIPT LANGUAGE="JavaScript">
<!--
        function popup_show(imageid)
        {
              var url;
              <?php
                echo 'url = "sponsoren_popup_show_gr.php?action=edit&imageid="+imageid+"&PHPSESSID='.session_id().'";';
              ?>
              window.open(url,"sponsoren","width=678, height=520, top=200, left=200, menubar=no, directories=no, resizeable=yes, toolbar=no, scrollbars=no, status=no");
        }
        
        function popup_sponsor(sponsorid)
{
                var url;
        if (sponsorid == 0)
        <?php
           	echo 'url = "sponsor_popup.php?action=add&PHPSESSID='.session_id().'";';
           echo ' else ';
           	echo 'url = "sponsor_popup.php?action=edit&sponsorid="+sponsorid+"&PHPSESSID='.session_id().'";';
        ?>

        window.open(url,"sponsor","width=650, height=550, top=200, left=200, menubar=no, directories=no, resizeable=yes, toolbar=no, scrollbars=yes, status=no");
}
-->
</SCRIPT>



<?php
sitename("sponsoren.php",$_SESSION["groupid"]);

switch ($_REQUEST["action"])
{
case 'edit':
	if (priv("sponsoren"))
	{
	
		echo '<CENTER><TABLE WIDTH="40%" BORDER="0">';
		echo '<TR>';
		echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
		echo '<a href="javascript:popup_sponsor(0);">';
		echo '<IMG SRC="images/new.jpg" BORDER="0" ALT="neu">';
		echo '</a>';
		echo '</TD>';
		echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
		echo '<a href="javascript:popup_sponsor(0);">';
		echo '<B>Sponsor hinzufügen</B>';
		echo '</a>';
		echo '</TD>';
		echo '</TR>';
		echo '</TABLE></CENTER><BR>';
	
		$sqlstr = "select id,firma,name,strasse,ort,image_kl,image_gr,www,www_link,gueltig_ab,gueltig_bis
		           from fb_sponsoren order by name";
		$result = GetResult($db,$sqlstr);
		//echo $sqlstr;
		echo '<TABLE WIDTH="100%" BORDER="0">';
			echo "<tr>";
			echo "<td BGCOLOR='#DDDDDD'><b>Name</td></b>";
			echo "<td BGCOLOR='#DDDDDD'><b>Firma</td></b>";
			echo "<td BGCOLOR='#DDDDDD'><b>Gueltig bis</td></b>";
			echo "<td BGCOLOR='#DDDDDD'><b>WWW</td></b>";
		        echo "<td BGCOLOR='#DDDDDD'><b>Logo kl.</td></b>";
		        echo "<td BGCOLOR='#DDDDDD'><b>Logo gr.</td></b>";
		        echo "<td BGCOLOR='#DDDDDD'><b>Edit</td></b>";
		        echo "<td BGCOLOR='#DDDDDD' COLSPAN='2'><b>del</b></td>";
		        echo "</tr>";
	        	        
	        
		        foreach ($result as $row)
			{
				echo '<tr><td ALIGN="CENTER">';
					if ($row["name"] !="" )
						echo $row["name"];
					else
						echo " - ";
				echo '</td><td ALIGN="CENTER">';
					if ($row["firma"] !="" )
						echo $row["firma"];
					else
						echo " - ";
				echo '</td><td ALIGN="CENTER">';
					echo $row["gueltig_bis"];
               	         echo '</td>';   	
				
				echo '<td ALIGN="CENTER">';
					if ($row["www_link"]==1)
						echo "ja";
					else
					   	echo "nein";
				echo '</td>';
		
				echo '<td ALIGN="CENTER">';
					if (! ($row["image_kl"]==0) || (! isset($row["image_kl"])))
						echo "ja";
					else
					   	echo "nein";
				echo '</td>';
		
			echo '<td ALIGN="CENTER">';
				if (! ($row["image_gr"]==0) || (! isset($row["image_gr"])))
					echo "ja";
				else
				   	echo "nein";
			echo '</td>';
		
		
		        echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                           echo '<a href="javascript:popup_sponsor('.$row["id"].');">';
                           echo '<IMG SRC="images/edit.jpg" BORDER="0" ALT="Bearbeiten">';
                	 	  echo '</a>';
                	   
                	   // Pruefen, ob ein grosses LOGO vorhanden ist 
                	   if ($row["image_gr"] != "0")
                	       echo " +";
                	   
                	   // Pruefen, ob Sponsor noch gültig ist
                	   $currentdate=date('Ymd');
                	   $gueltig_ab = str_replace("-","",$row["gueltig_ab"]);
					   $gueltig_bis = str_replace("-","",$row["gueltig_bis"]);
	        		   if (($currentdate <= $gueltig_bis) && ($currentdate >= $gueltig_ab))
                	       echo " *";
                	echo '</TD>';
                	
                        echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                        	echo '<INPUT TYPE="CHECKBOX" ID="chk'.$row["id"].'" onClick="enableDelete(this.id,\'del'.$row["id"].'\',\'emp'.$row["id"].'\')" />';
                        echo '</TD>';
                        echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                               echo '<DIV ID="emp'.$row["id"].'" STYLE="display:block;">';
                                   echo '<IMG SRC="images/empty.gif" BORDER="0" HEIGHT="16" WIDTH="16">';
                               echo '</DIV>';
                               echo '<DIV ID="del'.$row["id"].'" STYLE="display:none;">';
                                        echo '<A HREF="index.php?site=sponsoren&action=del&sponsorid='.$row["id"].'&PHPSESSID='.session_id().'">';
                                        	echo '<IMG SRC="images/del.gif" BORDER="0" HEIGHT="16" WIDTH="16">';
                                	echo '</A>';
                          	echo '</DIV>';
                          echo '</TD>';
                                
			echo '</tr>';
		
			}
		echo '</TABLE>';
		echo '<br>* Sponsor ist aktuell gültig';
	}
	else
	{
	   echo "<b>Sie haben keine Berechtigung die Sponsoren zu editieren !</b>";	
	}

break;	

case 'del':
	if (priv("sponsoren"))
	{
		$sponsorid = $_REQUEST["sponsorid"];
			
		// zugehörige Sponsorenbilder freigeben
		$sqlstr = "select image_kl,image_gr from fb_sponsoren where id=$sponsorid";
		$result = GetResult($db,$sqlstr);
		$image_kl = $result["0"]["image_kl"];
		$image_gr = $result["0"]["image_gr"];
		$sqlstr = "update sys_images set linked=0 where (image_id=$image_kl or image_id=$image_gr)";
		$result=doSQL($db,$sqlstr);
	
	        // Sponsordatensatz löschen	
		$sqlstr="delete from fb_sponsoren where id=$sponsorid";
		$result=doSQL($db,$sqlstr);
		mlog ("Ein Sponsor wurde aus der Sponsorenverwaltung gelöscht: ".$sponsorid);

		echo "<br>Sponsordaten erfolgreich gelöscht";
		echo "<br><br>Sollen die zugehörigen Sponsorenlogos auch gelöscht werden ?";
		echo "<br><br><br>";
		echo '<table ALIGN="CENTER">';
		echo '<tr>';
			janein_link('Ja','index.php?site=sponsoren&action=del_image&image_kl='.$image_kl.'&image_gr='.$image_gr.'&PHPSESSID='.session_id());
		 	janein_link('Nein','index.php?site=sponsoren&action=edit&PHPSESSID='.session_id());
		echo '</tr>';
		
		
	}
	else
	{
	   echo "<b>Sie haben keine Berechtigung die Sponsoren zu löschen !</b>";	
	}

break;

case 'del_image':
	if (priv("sponsoren"))
	{
		$image_kl=$_REQUEST["image_kl"];
		$image_gr=$_REQUEST["image_gr"];
		
		$sqlstr = "delete from sys_images where (image_id=$image_kl or image_id=$image_gr) and linked=0";
		$result = doSQL($db,$sqlstr);
		echo "Die Sponsorenbilder wurden gelöscht";
		mlog ("Sponsorenlogos wurden aus der Sponsorenverwaltung gelöscht: ".$image_kl." - ".$image_gr);
		echo '<br><br><table ALIGN="CENTER">';
		  echo '<table ALIGN="CENTER"><tr>';
		    janein_link('Ok','index.php?site=sponsoren&action=edit&PHPSESSID='.session_id());
	        echo '</tr></table>';
	}
	else
	{
		echo "<b>Sie haben keine Berechtigung diese Aktion auszuführen !</b>";	
	        
	}
break;


case 'list':
	
      echo '<table WIDTH="100%" BORDER=1>';
      $currentdate=date('Ymd');
     
      $sqlstr = "select name,image_kl,image_gr,www,www_link,gueltig_ab,gueltig_bis from fb_sponsoren"; 
      $result= GetResult($db,$sqlstr);
      $n=1;
      
      echo '<tr>';
      echo '<td ALIGN="CENTER"><br><h2>Werden Sie neuer<br> Sponsor des TuS...</h2><br><b>Ansprechpartner:';
      echo '<br><a><A HREF="mailto:beeken.ocholt@nwn.de">Peter Beeken</b>';
      echo '</td>';
      $n=2;
       
      // Array durchmischen
      shuffle($result);

      foreach ($result as $i)
      {
        $gueltig_ab = str_replace("-","",$i["gueltig_ab"]);
        $gueltig_bis = str_replace("-","",$i["gueltig_bis"]);
       if (($currentdate <= $gueltig_bis) && ($currentdate >= $gueltig_ab))
       {  
          echo '<td ALIGN="CENTER">';
          echo '<a href="javascript:popup_show('.$i["image_gr"].');">';
          echo '<IMG SRC="showimage2.php?id='.$i["image_kl"].'" BORDER="0">';
          echo '</a>';
          if ($i["www_link"]=="1")
             echo '<br><A HREF="'.$i["www"].'" TARGET="Sponsoren"><b>'.$i["www"].'</b></A>';
     
          echo '</td>';
          if ($n==3)
          {
             $n=0;
             echo '</tr><tr>';
          }
             $n++;
       }
      }
      echo '</tr>';
       
      echo '</table>';
break;
}

?>
<?php

// Oracle DB_Connection





/*
function decho ($text) {
        global $DEBUG;
        if ($DEBUG==1) echo '<br>'.$text;
}
*/
// DB-Kommunikation
//

function oconnect () {
global $user,$pass,$tns,$DEBUG;
        OCIInternalDebug(0);
        if ($conn = @OCILogon($user,$pass,$tns)) {
                decho ("<br><B>SUCCESS ! Connected to database <B>");
                return $conn;
        }
        else
        {
                decho ("<br><B>Failed : Could not connect to database<B>");
                return -1;
   }
}


function oclose($conn) {
  OCILogoff($conn);
}

function GetResult($conn,$sqlstr,$start="-1",$count="-1") {
        $stmt = ociparse($conn,$sqlstr);
        @ociexecute ($stmt);
        $e = OCIError($stmt);
       if ($e) {
			  decho($e["message"]." : ");
           decho($sqlstr);
			  return;
           die("<br>Fehler beim Ausführen des SQLStatements");
        }
        $i=0;
        $idx=0;
        $getmorerows="1";
        $result=array();
        while (ocifetchinto ($stmt,&$arr,OCI_ASSOC) && $getmorerows="1")
        {
			  if ($start!="-1" and $count!="-1") {
					if ($start<=$i and $idx<$count) {
						array_push($result,$arr);
						$idx++;
                  if ($idx==$count) {
                  	$getmorerows="0";
                  }
               }
           }	else {
	             array_push($result,$arr);
			  }
           $i++;
        }
        ocifreestatement($stmt);
		  if (count($result)==0)
				return;
        else
	        return $result;
}

function doSQL($conn,$sqlstr) {
        $stmt = ociparse($conn,$sqlstr);
        @ociexecute ($stmt);
		  $e = OCIError($stmt);
        if ($e) {
			  decho($e["message"]." : ");
           decho($sqlstr);
			  return $e;
           die("<br>Fehler beim Ausführen des SQLStatements");

        }
        ocifreestatement($stmt);
        return ;
}

function nextId ($conn) {
         $sqlstr = "select IDGENERATOR.NEXTVAL from DUAL";
         $result = GetResult($conn,$sqlstr);
         return $result["0"]["NEXTVAL"];
}
?>
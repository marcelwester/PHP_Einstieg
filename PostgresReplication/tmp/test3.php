<?php

if ($_GET["action"]=="save") {
	
	$x=unserialize(base64_decode($_POST["x"]));
	
	print_r($x);
	exit;
	foreach ($x as $y) {
		$f=fopen("testtar.dmp","wb");
		fputs($f,$y["cnt"]);
		fclose($f);
		echo "Client: ".strlen($y["cnt"]);
	}
	
		
	
	
	exit;
	
	$f=fopen("testtar.dmp","wb");
	fputs($f,$x["cnt"]);
	fclose($f);
	
	//print_r($_POST);
	
	

exit;
}

$db = new PDO('pgsql:dbname=test;host=127.0.0.1', 'vol', '');
$stmt = $db->prepare("select id, name,blob from sys_images where id=42");
$stmt->execute(array());
$stmt->bindColumn(2, $name, PDO::PARAM_STR, 256);
$stmt->bindColumn(3, $lob, PDO::PARAM_LOB);
$stmt->fetch(PDO::FETCH_BOUND);


// fpassthru($lob);

$indx=0; $x=null;
$x=null;
while (!feof($lob)) {
	$x.=fgetc($lob);
	$indx++;	
	
}

echo $indx."\n";
echo strlen($x)."\n";


$P=array();
$P["x"]["name"]="testfile.dmp";
$P["x"]["cnt"]="test"; //$x;
$P["x"]["id"]=42;

$x=array();
$xs=base64_encode(serialize($P));

//$x=http_build_query($x);


//echo strlen($xs)."\n";

$form_url = "http://127.0.0.1/dev/postrep/test3.php?action=save";
// Initialize cURL
$curl = curl_init();

// Set the options
curl_setopt($curl,CURLOPT_URL, $form_url);

// This sets the number of fields to post
//curl_setopt($curl,CURLOPT_POST, sizeof($data));

// This is the fields to post in the form of an array.
curl_setopt($curl,CURLOPT_POSTFIELDS, "x=".$xs);

//execute the post
$result = curl_exec($curl);

//close the connection
curl_close($curl);




exit;
//$lob = fopen('data://text/plain;base64,' . base64_encode($lob), 'r');

//echo $lob;

// while ($x=fgetc($lob)) {
// 	$cnt.=$x;
// }

exit;

echo "\n";
echo strlen($cnt);

echo "\n".$name."\n";
echo strlen($name)."\n";
?>
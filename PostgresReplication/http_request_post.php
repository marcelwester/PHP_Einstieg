<?php

// Define URL where the form resides
$form_url = "http://10.8.0.100/PostgresReplication/post.php";

// This is the data to POST to the form. The KEY of the array is the name of the field. The value is the value posted.
$data_to_post = array();
$data_to_post['username'] = 'Mickey';
$data_to_post['password'] = 'Minnie';
$data_to_post['word'] = 'XXX';

$txid = array();
$data = array();

$data["_txid"]["0"] = "txid";
$data["txid"]["0"]["table"] = "table x";
$data["txid"]["0"]["DML"] = "U";
$data["txid"]["0"]["MD5"] = "YYYYYY";
$data["txid"]["0"]["PK"]["0"] = "PK_1";
$data["txid"]["0"]["PK"]["1"] = "PK_2";
$data["txid"]["0"]["PK"]["2"] = "PK_3";
$data["txid"]["0"]["coldata"]["0"] = "data_field_1";
$data["txid"]["0"]["coldata"]["1"] = "data_field_1";
$data["txid"]["0"]["coldata"]["2"] = "data_field_1";
$data["txid"]["0"]["coldata"]["3"] = "data_field_1";
$data["txid"]["0"]["coldata"]["4"] = "data_field_1";
$data["txid"]["1"]["table"] = "table y";
$data["txid"]["1"]["DML"] = "U";
$data["txid"]["1"]["MD5"] = "YYYYYY";
$data["txid"]["1"]["PK"]["0"] = "PK_1";
$data["txid"]["1"]["PK"]["1"] = "PK_2";
$data["txid"]["1"]["PK"]["2"] = "PK_3";
$data["txid"]["1"]["coldata"]["0"] = "data_field_1";
$data["txid"]["1"]["coldata"]["1"] = "data_field_1";
$data["txid"]["1"]["coldata"]["2"] = "data_field_1";
$data["txid"]["1"]["coldata"]["3"] = "data_field_1";
$data["txid"]["1"]["coldata"]["4"] = "data_field_1";

$data["_txid"]["1"] = "txid1";
$data["txid1"]["0"]["table"] = "table x";
$data["txid1"]["0"]["DML"] = "U";
$data["txid1"]["0"]["MD5"] = "YYYYYY";
$data["txid1"]["0"]["PK"]["0"] = "PK_1";
$data["txid1"]["0"]["PK"]["1"] = "PK_2";
$data["txid1"]["0"]["PK"]["2"] = "PK_3";
$data["txid1"]["0"]["coldata"]["0"] = "data_field_1";
$data["txid1"]["0"]["coldata"]["1"] = "data_field_1";
$data["txid1"]["0"]["coldata"]["2"] = "data_field_1";
$data["txid1"]["0"]["coldata"]["3"] = "data_field_1";
$data["txid1"]["0"]["coldata"]["4"] = "data_field_1";
$data["txid1"]["1"]["table"] = "table y";
$data["txid1"]["1"]["DML"] = "U";
$data["txid1"]["1"]["MD5"] = "YYYYYY";
$data["txid1"]["1"]["PK"]["0"] = "PK_1";
$data["txid1"]["1"]["PK"]["1"] = "PK_2";
$data["txid1"]["1"]["PK"]["2"] = "PK_3";
$data["txid1"]["1"]["coldata"]["0"] = "data_field_1";
$data["txid1"]["1"]["coldata"]["1"] = "data_field_1";
$data["txid1"]["1"]["coldata"]["2"] = "data_field_1";
$data["txid1"]["1"]["coldata"]["3"] = "data_field_1";
$data["txid1"]["1"]["coldata"]["4"] = "data_field_1";




//print_r($data);

$data_to_post=http_build_query($data);

// Initialize cURL
$curl = curl_init();

// Set the options
curl_setopt($curl,CURLOPT_URL, $form_url);

// This sets the number of fields to post
//curl_setopt($curl,CURLOPT_POST, sizeof($data));

// This is the fields to post in the form of an array.
curl_setopt($curl,CURLOPT_POSTFIELDS, $data_to_post);

//execute the post
$result = curl_exec($curl);

//close the connection
curl_close($curl);





?>


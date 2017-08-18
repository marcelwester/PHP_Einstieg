<?php
  $dsn = 'pgsql:dbname=test;host=127.0.0.1';
  $user = 'vol';
  $password = ''; 
   // these variables must be set to match the actual connection
  try {
    $dbh = new PDO($dsn, $user, $password);
  } catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
  }
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  
/*  
// Stores a file:
  if($_GET['file']){ 
  $filename=$_GET['file']; // Normally, the file will probably be uploaded, but that's another howto  ;-)
  $data = bin2hex(file_get_contents($filename)); // This may be a problem on too large files
  try{ 
    $sql="insert into blobstore (doc,blob) values('test',?)";
    $sqlh= $dbh->prepare($sql);
    $sqlh->execute(array($data));
  }
  catch(Exception $e){
    die($e->getMessage());
  }
  print("<p>Done</p>");

}
*/

// reads a file out of the database:
$id=43;
  $sql="select blob from sys_images where id=?";
  $sqh=$dbh->prepare($sql);
  $sqh->execute(array($id));
  $data=$sqh->fetchAll(PDO::FETCH_NUM);
  $data=$data[0][0]; // print($data) here will just return "Resource id # ..."
  $data=fgets($data); // The data are returned as a stream handle gulp all of it in in one go, again, this may need some serious rework for too large files 
  echo strlen($data);
  //print(pack('H*',$data)); // reverses the bin2hex, no hex2bin in my php... ????


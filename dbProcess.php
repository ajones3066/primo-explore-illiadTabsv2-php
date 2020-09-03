<?php
// process.php

header('Access-Control-Allow-Origin: *');

$whitelist=array("localhost", "","");
$dbhost = "";
$dbuser = "";
$database = "";
$dbpassword = "";
/******** End variable to edit ******************/

$domain=parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
if (!in_array($domain, $whitelist)){exit();}

$conn=new mysqli($dbhost,$dbuser,$dbpassword,$database);

$errors = array();  // array to hold validation errors

$data = array(); //array to hold the data

$pnxRecordID = mysqli_real_escape_string($conn,$_POST['pnxRecordId']);
$userID = mysqli_real_escape_string($conn,$_POST['userID']);
$patronType = mysqli_real_escape_string($conn,$_POST['patronType']);
$formatPreference = mysqli_real_escape_string($conn,$_POST['formatPreference']);
$processType = 'borrowing';
$requestType = mysqli_real_escape_string($conn,$_POST['requestType']);
$type = mysqli_real_escape_string($conn,$_POST['type']);
$author = mysqli_real_escape_string($conn,rtrim($_POST['author'],'.'));
$aulast = mysqli_real_escape_string($conn,$_POST['aulast']);
$aufirst = mysqli_real_escape_string($conn,$_POST['aufirst']);
$auinit = mysqli_real_escape_string($conn,$_POST['auinit']);
$auinit1 = mysqli_real_escape_string($conn,$_POST['auinit1']);
$auinitm = mysqli_real_escape_string($conn,$_POST['auinitm']);
$ausuffix = mysqli_real_escape_string($conn,$_POST['ausuffix']);
$aucorp = mysqli_real_escape_string($conn,$_POST['aucorp']);
$title = mysqli_real_escape_string($conn,$_POST['title']);
$format = mysqli_real_escape_string($conn,$_POST['format']);
$isbn = mysqli_real_escape_string($conn,$_POST['isbn']);
$issn = mysqli_real_escape_string($conn,$_POST['issn']);
$sici = mysqli_real_escape_string($conn,$_POST['sici']);
$coden = mysqli_real_escape_string($conn,$_POST['coden']);
$eissn = mysqli_real_escape_string($conn,$_POST['eissn']);
$oclcid = mysqli_real_escape_string($conn,$_POST['oclcid']);
$genre = mysqli_real_escape_string($conn,$_POST['genre']);
$identifier = mysqli_real_escape_string($conn,$_POST['identifier']);
$atitle = mysqli_real_escape_string($conn,$_POST['atitle']);
$jtitle = mysqli_real_escape_string($conn,$_POST['jtitle']);
$stitle = mysqli_real_escape_string($conn,$_POST['stitle']);
$btitle = mysqli_real_escape_string($conn,$_POST['btitle']);
$pages = mysqli_real_escape_string($conn,$_POST['pages']);
$spage = mysqli_real_escape_string($conn,$_POST['spage']);
$epage = mysqli_real_escape_string($conn,$_POST['epage']);
$volume = mysqli_real_escape_string($conn,$_POST['volume']);
$issue = mysqli_real_escape_string($conn,$_POST['issue']);
$publisher = mysqli_real_escape_string($conn,$_POST['publisher']);
$cop = mysqli_real_escape_string($conn,$_POST['cop']);
$date = mysqli_real_escape_string($conn,$_POST['pubDate']);
$url = mysqli_real_escape_string($conn,$_POST['url']);
$link = mysqli_real_escape_string($conn,$_POST['openurl']);
$doi = mysqli_real_escape_string($conn,$_POST['doi']);
$notes = mysqli_real_escape_string($conn,$_POST['notes']);
$exactEdition = mysqli_real_escape_string($conn,$_POST['exactEdition']);
$permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
$magicLink = substr(str_shuffle($permitted_chars), 0, 32);
//$magicLink = uniqid(rand(), true);
$pickupLocation = mysqli_real_escape_string($conn,$_POST['pickupLocations']);

// let's put this into the database

$sql = "INSERT INTO requests (pnxRecordID,userID,patronType,processType,requestType,formatPreference,type,author,aulast,aufirst,auinit,auinit1,auinitm,ausuffix,aucorp,title,format,isbn,issn,sici,coden,eissn,oclcid,genre,identifier,atitle,jtitle,stitle,btitle,pages,spage,epage,volume,issue,publisher,cop,pubDate,url,link,doi,notes,requestStatus,pickupLocation,dateCreated,exactEdition, magicLink) VALUES ('$pnxRecordID','$userID','$patronType','$processType','$requestType','$formatPreference','$type','$author','$aulast','$aufirst','$auinit','$auinit1','$auinitm','$ausuffix','$aucorp','$title','$format','$isbn','$issn','$sici','$coden','$eissn','$oclcid','$genre','$identifier','$atitle','$jtitle','$stitle','$btitle','$pages','$spage','$epage','$volume','$issue','$publisher','$cop','$date','$url','$link','$doi','$notes','new','$pickupLocation',now(),'$exactEdition','$magicLink')";

if ($conn->query($sql) === TRUE) {
    $errorState = 1;
} else {
    $errorState = 0;
}

$conn->close();

if ($errorState == 1) {

  // if there are no errors, return a message
  $data['success'] = true;
  $data['message'] = "Success! Your request has been received.  You will receive an email when the request is processed.";
  //$data['message'] = "Here are the variables:".$pnxRecordID."<br \/>".$userID."<br \/>".$formatPreference."<br \/>".$type;
}

else {

  $data['success'] = false;
  $data['message'] = "Here are the variables:".$pnxRecordID."<br \/>".$userID."<br \/>".$formatPreference."<br \/>".$type;


}

// return all our data to an AJAX call
echo json_encode($data);


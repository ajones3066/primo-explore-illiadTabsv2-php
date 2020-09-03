<?php

$key="";    /* enter API key here  */
$illiadDomain=""; /* e.g. illiad.myinst.edu */

/* Read Database Transactions */

$dbhost = "";
$dbuser = "";
$database = "";
$dbpassword = "";

/* END Variables */

$conn=new mysqli($dbhost,$dbuser,$dbpassword,$database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

// get a set of records

 $sql = "SELECT * FROM requests WHERE requestStatus = 'new' AND patronType <= '38';";

 $resultQuery = $conn->query($sql);

//loop through new records

if ($resultQuery->num_rows > 0) {
// output data of each row
while($row = $resultQuery->fetch_assoc()) {

/* Create ILLIAD Transactions */
$requestId = $row["requestID"];
$username = $row["userID"];
$processType = $row["processType"];
$requestType = $row["requestType"];
$author = $row["author"]; //LoanAuthor or ArticleAutheor?
$aulastName = $row["aulast"];
$aufirstName = $row["aufirst"];
$auCorp = $row["aucorp"];
$title = $row["title"]; //LoanAuthor or ArticleAutheor?
$format = $row["format"];
$isbn = $row["isbn"];
$issn = $row["issn"];
$sici = $row["sici"];
$coden = $row["coden"];
$eissn = $row["eissn"];
$oclcid = $row["oclcid"];
$genre = $row["genre"];
$identifier = $row["identifier"];
$atitle = $row["atitle"];
$jtitle = $row["jtitle"];
$btitle = $row["btitle"];
$seriesTitle = $row["stitle"];
$pages = $row["pages"];
$spage = $row["spage"];
$epage = $row["epage"];
$volume = $row["volume"];
$issue = $row["issue"];
$publisher = $row["publisher"];
$place = $row["cop"];
$pubDate = $row["pubDate"];
$number = $row["number"];
$date = $row["date"];
$doi = $row["doi"];
$notes = $row["notes"];
$pickupLocation = $row["pickupLocation"];
$pmid = $row["pmid"];
$ssn = $row["ssn"];
$part = $row["part"];
$quarter = $row["quarter"];
$exactEdition = $row["exactEdition"];
$bibcode = $row["pnxRecordID"];
$transactionStatus = 'TNS Unfilled Review';

//need function to figure out ILLIAD author

if ($requestType === 'article') {

	$articleAuthor = $author;
	$articleTitle = $atitle;
	$PhotoJournalTitle = $jtitle;
	$PhotoItemPublisher = $publisher;
	$PhotoItemPlace = $place;

list($year,$month,$day)=explode('-',$pubDate);
$photoJournalMonth = $month;
$photoJournalYear = $year;
$photoJournalday = $day;

if ($pages === ''){
$pages = $spage."-".$epage;
}

} else {

	$loanAuthor = $author;
	$loanTitle = $btitle;
	$loanDate = $pubDate;
    $loanPublisher = $publisher;
    $loanPlace = $place;
}

//need function to figure out ISxN

if ($isbn <> '') {
 $isxn = $isbn;
} else $isxn = $issn;

//need function to figure out exactEdition

$payload = json_encode(array(
	"Username"=> $username,
	"RequestType"=>$requestType,
	"ProcessType"=>$processType,
	"DocumentType"=>$genre,
	"LoanAuthor"=>$loanAuthor,
	"LoanTitle"=>$btitle,
	"LoanPublisher"=>$loanPublisher,
	"LoanPlace"=>$loanPlace,
	"LoanDate"=>$loanDate,
	"LoanEdition"=>$loanEdition,
	"PhotoJournalTitle"=>$jtitle,
	"PhotoArticleTitle"=>$atitle,
	"PhotoJournalMonth"=>$photoJournalMonth,
	"PhotoJournalYear"=>$photoJournalYear,
	"PhotoJournalInclusivePages"=>$pages,
	"PhotoItemPublisher"=>$PhotoItemPublisher,
	"PhotoItemPlace"=>$PhotoItemPlace,
	"PhotoArticleAuthor"=>$articleAuthor,
	"PhotoItemAuthor"=>$articleAuthor,
	"PhotoJournalVolume"=>$volume,
	"PhotoJournalIssue"=>$issue,
	"AcceptAlternateEdition"=>$exactEdition,
	"ISSN"=>$isxn,
	"DeliveryMethod"=>'Hold for Pickup',
	"PMID"=>$pmid,
	"DOI"=>$doi,
	"ItemInfo1"=>$bibcode,
	"TransactionStatus"=>$transactionStatus));

$illiadURL = 'https://'.$illiadDomain.'/ILLiadWebPlatform/transaction/';
$illiadUpdate = curl_init( $illiadURL );
curl_setopt( $illiadUpdate, CURLOPT_POSTFIELDS, $payload );
curl_setopt( $illiadUpdate, CURLOPT_HTTPHEADER, array('Content-Type:application/json',"ApiKey: $key"));
curl_setopt( $illiadUpdate, CURLOPT_RETURNTRANSFER, true );


$illiadUpdateResult = curl_exec($illiadUpdate);
curl_close($illiadUpdate);

$result = json_decode($illiadUpdateResult, true);
print_r($result);

//Update record status

 if ($illiadUpdateResult['TransactionNumber']) {
 	$requestStatus = $result['TransactionNumber'];
 } else $requestStatus = 'ERROR';

$updateSQL = 'UPDATE requests SET requestStatus = "'.$requestStatus.'" WHERE requestID ="'.$requestId.'";';

 if ($conn->query($updateSQL) === TRUE) {
     	echo "Record updated successfully";
			} else {
     	echo "Error updating record: " . $conn->error;
 			}

      }
  } else {
      echo "0 results";

// //end loop
}

//close database connection
$conn->close();

?>
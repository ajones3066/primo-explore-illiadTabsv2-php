<?php
// process.php
header('Access-Control-Allow-Origin: *');

$whitelist=array("localhost", "","");
$dbhost = "localhost";
$dbuser = "";
$database = "";
$dbpassword = "";
/******** End variable to edit ******************/

$domain=parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
if (!in_array($domain, $whitelist)){exit();}

$conn=new mysqli($dbhost,$dbuser,$dbpassword,$database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$errors = array();  // array to hold validation errors

$userData = array(); //array to hold the data

$userID = mysqli_real_escape_string($conn,$_POST['userID']);

// let's query the database

$sql = 'SELECT * FROM users WHERE userID LIKE "'.$userID.'";';

if ($result = $conn->query($sql)) {

$row_cnt = $result->num_rows;

if ($row_cnt === 1){
$row = mysqli_fetch_assoc($result);
$userData['tab']= 1;
$userData['patronType']= $row['patronType'];
} else $userData['tab'] = 3;

$result->close();
}

$conn->close();

// return all our data to an AJAX call
echo json_encode($userData);

?>

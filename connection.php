<?php
$host = "localhost"; 
$dbUsername = "root";  
$dbPassword = "";  
$dbName = "company_db";  

$conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);
if($conn){ echo "Connected"; } else { echo "Not Connected"; }   

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

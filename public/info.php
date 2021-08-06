<?php
$servername = "172.29.161.239";
$username = "kamal";
$password = "vihan123";
$dbname = "quant";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed-: " . $conn->connect_error);
}

die('fffffffffff');
?>

<?php
$servername = "database-mysql.c5k4qu6uo2ez.ap-southeast-2.rds.amazonaws.com";  // Replace with your RDS endpoint
$username = "admin";                      // Replace with your RDS username
$password = "6996Kong";                      // Replace with your RDS password
$dbname = "mobile";                   // Replace with your database name
$port = 3306;                                     // Replace with your RDS port if different

// Set PHP timezone before database connection
date_default_timezone_set('Asia/Kuala_Lumpur');

$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set MySQL session timezone
$timezone = "SET time_zone = '+08:00'";
$conn->query($timezone);

// Set charset to ensure proper handling of special characters
$conn->set_charset("utf8mb4");
?>
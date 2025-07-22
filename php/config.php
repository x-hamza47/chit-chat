<?php

require_once __DIR__ . '/../config/bootstrap.php';

$dbHost = $_ENV['DB_HOST'];
$dbName = $_ENV['DB_NAME'];
$dbUser = $_ENV['DB_USER'];
$dbPass = $_ENV['DB_PASS'];

// $dbHost = "localhost";
// $dbUser = "root";
// $dbPass = "";
// $dbName = "chat_app";

$conn = new mysqli($dbHost , $dbUser , $dbPass , $dbName);

if(!$conn){
    echo "Database Connection Error : " .$conn->connect_error;
}

?>
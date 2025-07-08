<?php  

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chat_app";

$conn = new mysqli($servername , $username , $password , $dbname);

if(!$conn){
    echo "Database Connection Error : " .$conn->connect_error;
}

?>
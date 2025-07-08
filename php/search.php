<?php
session_start();
include_once "config.php";

$search_term = $conn->real_escape_string($_POST['search']); 
$outgoing_id = $_SESSION['unique_id'];
$output = "";

$sql = "SELECT * FROM users WHERE NOT unique_id = {$outgoing_id} AND (fname LIKE '%{$search_term}%' OR lname LIKE '%{$search_term}%')";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    include "data.php";
}else{
    echo "No users were found related to your search term.";
}
echo $output;



?>
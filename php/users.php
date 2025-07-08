<?php

    session_start();
    include "config.php";
    if(isset($_SESSION['unique_id'])){
    $outgoing_id = $_SESSION['unique_id'];
    $sql = "SELECT * FROM users WHERE NOT unique_id = {$outgoing_id} ORDER BY id DESC";
    $result = $conn->query($sql);
    $output = "";

    if($result->num_rows == 0){
        $output .= "No users are available to chat";
    }elseif($result->num_rows > 0){
        include_once "data.php";
    }

    echo $output;
    $result->close();
    $conn->close();

    }else{
        echo "Session not set";
    }

?>
<?php

    session_start();
    include "config.php";
    if(isset($_SESSION['unique_id'])){
    $outgoing_id = $_SESSION['unique_id'];
    $sql = "SELECT u.*, MAX(m.created_at) AS latest_msg FROM users u
    LEFT JOIN (
        SELECT * FROM messages
        WHERE incoming_msg_id = {$outgoing_id}
        OR outgoing_msg_id = {$outgoing_id}
    ) 
    m ON u.unique_id = IF(m.incoming_msg_id = {$outgoing_id}, m.outgoing_msg_id, m.incoming_msg_id)
    WHERE NOT u.unique_id = {$outgoing_id}
    GROUP BY u.unique_id
    ORDER BY latest_msg DESC";

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
<?php

// session_start();
// if(isset($_SESSION['unique_id'])){
//     include_once "config.php";
//     $outgoing_id = $conn->real_escape_string($_POST['outgoing_id']);
//     $incoming_id = $conn->real_escape_string($_POST['incoming_id']);
//     $message = $conn->real_escape_string($_POST['message']);

//     if(!empty($message)){
//         $sql = $conn->prepare("INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg)
//         VALUES (?, ?, ?)");
//         $sql->bind_param("iis", $incoming_id, $outgoing_id, $message);
//         if($sql->execute()){
//             echo "success";
//         }else{
//             echo "Error : " . $sql->error;
//         }
//     }else{
//         echo "Message is empty";
//     }

// }else{
//     header("Location: ../index.php");
// }

?>
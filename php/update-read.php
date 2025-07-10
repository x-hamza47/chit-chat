<?php

include_once "config.php";

$incoming_id = $_POST['incoming_id'] ?? '';
$outgoing_id = $_POST['outgoing_id'] ?? '';

if (!empty($incoming_id) && !empty($outgoing_id)) {
    $updateRead = $conn->prepare("UPDATE messages SET is_read = true 
     WHERE incoming_msg_id = ? AND outgoing_msg_id = ? AND is_read = false");
    $updateRead->bind_param("ii", $incoming_id, $outgoing_id);
    $updateRead->execute();
    $updateRead->close();
}
$conn->close();

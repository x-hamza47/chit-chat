<?php
//  $you = '';
while($row = $result->fetch_assoc()){   
     $you = '';
                  
        $sql2 = $conn->prepare("SELECT * FROM messages WHERE (incoming_msg_id = ? 
          OR outgoing_msg_id = ? ) AND (outgoing_msg_id = ? 
          OR incoming_msg_id = ? ) ORDER BY created_at DESC LIMIT 1");

        $sql2->bind_param("iiii" ,$row['unique_id'], $row['unique_id'], $outgoing_id, $outgoing_id);
        $sql2->execute();

        $data = $sql2->get_result();

        $row2 = $data->fetch_assoc();

        $result2 = ($data->num_rows > 0) ? $row2['msg'] : "No messages available.";

        $msg = (strlen($result2) > 24) ?  substr($result2, 0 ,24) . "..." : $result2;

        if(isset($row2['outgoing_msg_id'])){
            $you = ($outgoing_id == $row2['outgoing_msg_id']) ? "<b style='font-weight: 600;'>You:</b> " : "";
        }
        
        // ! Unread Messages
        $unread_count = 0;
        $checkUnread = $conn->prepare("SELECT COUNT(id) AS unread FROM messages 
        WHERE incoming_msg_id = ? AND outgoing_msg_id = ? AND is_read = false");
        $checkUnread->bind_param("ii", $outgoing_id, $row['unique_id']);
        $checkUnread->execute();
        $res_unread = $checkUnread->get_result();

        if ($rowUnread = $res_unread->fetch_assoc()){
            $total_unread_msgs = $rowUnread['unread'] ;
        }
        $notification_dot = ($total_unread_msgs > 0) ? "<div class='notification-dot'>". ($total_unread_msgs > 99 ? '99+' : $total_unread_msgs)."</div>" : '';



    // $offline = ($row['status'] == "Offline") ? "offline" : "";
    $online = ($row['status'] == "Active") ? "online" : "";

    $output .=  '<a href="chat.php?user_id='.$row['unique_id'].'">
                <div class="content">
                <img src="php/upload/'. $row['img'] . '" alt="" class="list-imgs ' . $online . '" id="status-' . $row['unique_id'] . '">
                <div class="details">
                    <span>'. $row['fname'] . " " . $row['lname'] .'</span>
                    <p id="lastmsg-'. $row['unique_id'] .'">'. $you . $msg . '</p>
                    <div class="typing" id="typing-' . $row['unique_id'] . '" style="display: none;">typing...</div>
                </div>
                </div>
                '.$notification_dot.'
                </a>'  
                ;
}

?>
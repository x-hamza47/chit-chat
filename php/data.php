<?php
//  $you = '';
while($row = $result->fetch_assoc()){   
     $you = '';
                  
        $sql2 = $conn->prepare("SELECT * FROM messages
            WHERE (outgoing_msg_id = ? AND incoming_msg_id = ?)
               OR (outgoing_msg_id = ? AND incoming_msg_id = ?)
            ORDER BY created_at DESC LIMIT 1");

        $sql2->bind_param("ssss" ,$row['unique_id'], $outgoing_id, $outgoing_id, $row['unique_id']);
        $sql2->execute();

        $data = $sql2->get_result();

        $row2 = $data->fetch_assoc();

        $result2 = ($data->num_rows > 0) ? $row2['msg'] : "No messages available.";

        $msg = (strlen($result2) > 24) ?  substr($result2, 0 ,24) . "..." : $result2;

        if(isset($row2['outgoing_msg_id']) && $outgoing_id == $row2['outgoing_msg_id']){

        $is_read = $conn->prepare("SELECT is_read FROM messages WHERE id = ? LIMIT 1");
        $is_read->bind_param("s", $row2['id']);
        $is_read->execute();
        $res_read = $is_read->get_result();
        $rowRead = $res_read->fetch_assoc();

        $tickColor = ($rowRead && $rowRead['is_read']) ? '#4fa3f7' : '#ccc'; 
        // $you = ($outgoing_id == $row2['outgoing_msg_id']) ? "<b style='font-weight: 600;'>You:</b> " : "";
        $you = '<span class="tick-icon">
                      <svg width="15" height="15" viewBox="3 0 25 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M4 12L8 16L20 4" stroke="' . $tickColor . '" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                          <path d="M11 12L15 16L27 4" stroke="' . $tickColor . '" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                  </span>';
        }
    $time = isset($row2['created_at']) ? date('h:i A', strtotime($row2['created_at'])) : '';
    
    // ! Unread Messages
    $unread_count = 0;
        $checkUnread = $conn->prepare("SELECT COUNT(id) AS unread FROM messages 
        WHERE incoming_msg_id = ? AND outgoing_msg_id = ? AND is_read = false");
        $checkUnread->bind_param("ss", $outgoing_id, $row['unique_id']);
        $checkUnread->execute();
        $res_unread = $checkUnread->get_result();

        if ($rowUnread = $res_unread->fetch_assoc()){
            $total_unread_msgs = $rowUnread['unread'] ;
        }
        $notification_dot = ($total_unread_msgs > 0) ? 
        "<div class='notification-dot'>
            <div class='meta'>
                <span class='time'>" . $time . "</span>
            </div>
            ". ($total_unread_msgs > 99 ? '99+' : $total_unread_msgs)."
        </div>" 
        : '';



    // $offline = ($row['status'] == "Offline") ? "offline" : "";
    $online = ($row['status'] == "Active") ? "online" : "";

    $output .=  '<a href="chat.php?user_id='.$row['unique_id'].'">
                <div class="content">
                <img src="upload/'. $row['img'] . '" alt="" class="list-imgs ' . $online . '" id="status-' . $row['unique_id'] . '">
                <div class="details">
                    <span>'. $row['fullname'] .'</span>
                    <p id="lastmsg-'. $row['unique_id'] .'">'. $you . $msg . '</p> 
                    <div class="typing" id="typing-' . $row['unique_id'] . '" style="display: none;">typing...</div>
                </div>
                </div>
                '.$notification_dot.'
                </a>'  
                ;
}

?>
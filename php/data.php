<?php
 $you = '';
while($row = $result->fetch_assoc()){   
                  
        $sql2 = $conn->prepare("SELECT * FROM messages WHERE (incoming_msg_id = ? 
          OR outgoing_msg_id = ? ) AND (outgoing_msg_id = ? 
          OR incoming_msg_id = ? ) ORDER BY id DESC LIMIT 1");

        $sql2->bind_param("iiii" ,$row['unique_id'], $row['unique_id'], $outgoing_id, $outgoing_id);
        $sql2->execute();

        $data = $sql2->get_result();
        
        $row2 = $data->fetch_assoc();

        $result2 = ($data->num_rows > 0) ? $row2['msg'] : "No messages available.";

        $msg = (strlen($result2) > 28) ?  substr($result2, 0 ,28) . "..." : $result2;

        if(isset($row2['outgoing_msg_id'])){
            $you = ($outgoing_id == $row2['outgoing_msg_id']) ? "<b>You:</b> " : "";
        }
        

    // $offline = ($row['status'] == "Offline") ? "offline" : "";
    $online = ($row['status'] == "Active") ? "online" : "";

    $output .=  '<a href="chat.php?user_id='.$row['unique_id'].'">
                <div class="content">
                <img src="php/upload/'. $row['img'] . '" alt="" class="list-imgs ' . $online . '" id="status-' . $row['unique_id'] . '">
                <div class="details">
                    <span>'. $row['fname'] . " " . $row['lname'] .'</span>
                    <p>'. $you . $msg .'</p>
                </div>
                </div>
                <div class="notification-dot">
                2
                    
                </div>
                </a>'  
                ;
}

?>
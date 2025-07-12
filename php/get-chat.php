<?php

session_start();
if(isset($_SESSION['unique_id'])){
    include_once "config.php";
    $outgoing_id = $conn->real_escape_string($_POST['outgo_id']);
    $incoming_id = $conn->real_escape_string($_POST['inco_id']);
    $output = "";
    
$sql = $conn->query("SELECT * FROM messages
                    WHERE (outgoing_msg_id = {$outgoing_id} AND  incoming_msg_id = {$incoming_id})
                    OR (outgoing_msg_id = {$incoming_id} AND  incoming_msg_id = {$outgoing_id}) ORDER BY messages.id") ;
    if ($sql->num_rows > 0) {
        while($row = $sql->fetch_assoc()){

            $time = date('h:i A',strtotime($row['created_at']));
            $readed = ($row['is_read']) ? 'readed' : '';

            if($row['outgoing_msg_id'] === $outgoing_id){
                $output .= '<div class="chat outgoing">
                            <div class="details">
                                <p>'. $row['msg'] . '</p>
                                
                                <div class="meta">
                                    <span class="time">'.$time.'</span>
                                    <span class="tick-icon '.$readed.'">
                                        <svg width="15" height="15" viewBox="3 0 25 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M4 12L8 16L20 4" stroke="#ccc" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M11 12L15 16L27 4" stroke="#ccc" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                            
                            </div>';

            }else{
                $output .= '
                <div class="chat incoming">
                            <div class="details">
                                <p>'. $row['msg'] . '</p>
                                <div class="meta">
                                    <span class="time">' . $time . '</span>
                                </div>
                            </div>
                            </div>';
       
            }
        }
        echo $output;
    }
    $sql->close();


}else{
    header("Location: ../index.php");
}




?>
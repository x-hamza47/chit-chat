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
            if($row['outgoing_msg_id'] === $outgoing_id){
                $output .= '<div class="chat outgoing">
                            <div class="details">
                                <p>'. $row['msg'] .'</p>
                            </div>
                            </div>';
            }else{
                $output .= '<div class="chat incoming">
                            <div class="details">
                                <p>'. $row['msg'] .'</p>
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
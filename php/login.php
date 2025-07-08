<?php

session_start(); #-- Session start
include_once "config.php";  #-- connection file
#--- form data
$uname = $conn->real_escape_string($_POST['uname']);
$pass = $conn->real_escape_string($_POST['pass']);
#---end---#

if (!empty($uname) && !empty($pass)) { #-- Username and password input

    #-- Query
    $sql = $conn->prepare("SELECT unique_id, fname, lname, img,status  FROM users WHERE uname = ? AND password = md5(?)");
    $sql->bind_param('ss', $uname, $pass);
    $sql->execute();
    $result = $sql->get_result()->fetch_all(MYSQLI_ASSOC);

    if (count($result) > 0) {
        $status = "Active";
        $sql2 = $conn->prepare("UPDATE users SET status = ? WHERE unique_id = ?");
        $sql2->bind_param("si", $status, $result[0]['unique_id']);
        // ! Sessions
        if($sql2->execute()){
            $_SESSION['unique_id'] = $result[0]['unique_id'];
            $_SESSION['last_online'] = time();
            echo "success";
        }

    }else{
        echo "Username or Password is Incorrect. ";
    }

}else{
    echo "All fields are required";
}


?>
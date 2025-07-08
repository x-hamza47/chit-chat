<?php

	// session start
	session_start();
  if(isset($_SESSION['unique_id'])){
    include "config.php";
    $logout_id = $conn->real_escape_string($_GET['logout_id']);
    if(isset($logout_id)){

      $status = "Offline";
      
      $sql = $conn->prepare("UPDATE users SET status = ? WHERE unique_id = ?");
      $sql->bind_param("si", $status, $logout_id);

      if($sql->execute()){
        session_unset();
        session_destroy();
        header("location: ../index.php");
      }

    }else{
      header("location: ../users.php");
    }


  }else{
    header("location: ../index.php");

  }





?>
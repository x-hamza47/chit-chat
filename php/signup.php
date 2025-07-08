<?php
  #-- Session Start
    session_start();
include "config.php";  #-- connection file
#--- form data
$fname = $conn->real_escape_string($_POST['fname']);
$lname = $conn->real_escape_string($_POST['lname']);
$uname = $conn->real_escape_string($_POST['uname']);
$pass = $conn->real_escape_string($_POST['pass']);
#---end---#

if (!empty($fname) && !empty($lname) && !empty($uname) && !empty($pass)) {

    // Username validation
    if (preg_match('/^(?=[a-zA-Z0-9]*[._@])[a-zA-Z0-9._@]{3,30}$/', $uname)) {
 
        #-- if username exists 
        #-- Query
      $sql = $conn->prepare("SELECT uname FROM users WHERE uname = ?");
      $sql->bind_param('s' , $uname);
      $sql->execute();
      $result = $sql->get_result();

        if($result->fetch_assoc()){    #*--get result
            echo "$uname -  already exists ";     #*--exists error
        }else{    #---   Continue to Insert query

        #---Checking User Upload File
        if(!empty($_FILES['image']['name'])){

            $img_name = $_FILES['image']['name'];
            $img_type = $_FILES['image']['type'];
            $img_size = $_FILES['image']['size']; #-- coming soon
            $img_tmp = $_FILES['image']['tmp_name'];

            $extensions = ['png', 'jpg', 'jpeg']; #-- valid extensions

            #-- get file extension 
            $img_break = explode('.',$img_name);
            $img_ext = strtolower(end($img_break));
    
            #-- file extension check
            if (in_array($img_ext , $extensions) == false) {
                echo "This $img_ext extension is not allowed. Please select JPEG, PNG, or JPG files.";
            }else{#--extension checkpoint
            
            $new_name = time() . "-" . basename($img_name); #-- new name for file
            $target = "upload/".$new_name; #--- upload folder location

             #-- Uploading File
                if(move_uploaded_file($img_tmp , $target)){
                    //  $status = "Active";
                     $random_id = rand(time(), 1000000);

                    #-- Insert data 
                    $sql2 = $conn->prepare("INSERT INTO users (unique_id, fname, lname, uname, password, img)  VALUES ( ?, ?, ?, ?,md5(?), ? )");
                    $sql2->bind_param("isssss" , $random_id, $fname, $lname, $uname, $pass, $new_name);

                        if($sql2->execute()){#-- if insert query executes
                            #-- get unique id for session
                            $sql3 = $conn->prepare("SELECT unique_id FROM users WHERE uname = '{$uname}'");
                            $sql3->execute();
                            $sql3->bind_result($signup_id);
                            if($sql3->fetch() > 0){
                                echo "Success";
                            }else{
                                echo "This username does not exists";
                            }#-- session get end
                        }
                } #-- Uploading end

            } #-- file extension check end
        }else{ #-- !set file
            echo  "Please select an image file!";
        } #-- file condition end


      } #-- if username does not exist end

    }else{ #-- Not a valid username
        echo  "$uname - Invalid username format! ";
    }#-- email filter validation end
    
}else{ #-- if fields are empty
    echo "All fields are required";
}#-- end

?>
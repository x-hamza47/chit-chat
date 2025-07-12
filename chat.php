<?php
session_start();
include_once "php/config.php";
if (!isset($_SESSION['unique_id'])) {
    header("Location: index.php");
}
// elseif(time() - $_SESSION['last_online'] > 900){
//     $status = "offline";

//     $sql2 = $conn->prepare("UPDATE users SET status = ? WHERE unique_id = ?");
//     $sql2->bind_param("si", $status, $_SESSION['unique_id']);

//     if($sql2->execute()){
//       session_unset();
//       session_destroy();
//       header("location: index.php");
//     }
// }
?>

<?php include_once "header.php"; ?>

<body>
    <div class="container">
        <div class="wrapper">
            <section class="chat-area">
                <!-- Header -->
                <header>
                    <?php

                    $user_id = $conn->real_escape_string($_GET['user_id']);
                    $sql = "SELECT unique_id, fname, lname, img, status FROM users WHERE unique_id = {$user_id}";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                    } else {
                        header("location: users.php");
                    }

                    $outgoing_id = $user_id;
                    $incoming_id = $_SESSION['unique_id'];

                    $updateRead = $conn->prepare("UPDATE messages SET is_read = true 
                         WHERE incoming_msg_id = ? AND outgoing_msg_id = ? AND is_read = false");
                    $updateRead->bind_param("ii", $incoming_id, $outgoing_id);
                    $updateRead->execute();
                    $online = ($row['status'] == 'Active') ? 'online' : '';
                    ?>
                    <!-- Content -->
                    <a href="users.php" class="back-icon"><i class="fas fa-arrow-left"></i></a>
                    <img src="php/upload/<?php echo $row['img'];  ?>" alt="" class="img <?php echo $online;  ?>" id="status-<?php echo $outgoing_id; ?>">
                    <div class="details">
                        <span><?php echo $row['fname'] . " " . $row['lname'];  ?></span>
                        <p id="user-status"><?php echo $row['status']; ?> </p>
                    </div>
                </header>
                <div class="chat-box">
                </div>

                <form class="typing-area">
                    <input type="hidden" name="outgoing_id" id="out" value="<?php echo $incoming_id; ?>" hidden>
                    <input type="hidden" name="incoming_id" id="in" value="<?php echo $user_id; ?>" hidden>
                    <input type="text" name="message" class="input-field" placeholder="Type a message here..." autocomplete="off" autofocus>
                    <button><i class='bx bxs-send'></i></button>

                </form>

            </section>
        </div>
    </div>
    <script src="javascript/chat.js"></script>

</body>


</html>
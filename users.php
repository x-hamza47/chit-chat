<?php
session_start();
include_once "php/config.php";
if (!isset($_SESSION['unique_id'])) {
    header("Location: index.php");
}
// elseif (time() - $_SESSION['last_online'] > 900) {
//     $status = "Offline";

//     $sql2 = $conn->prepare("UPDATE users SET status = ? WHERE unique_id = ?");
//     $sql2->bind_param("si", $status, $_SESSION['unique_id']);
//     $sql2->execute();

//     if ($sql2->execute()) {
        // session_unset();
        // session_destroy();
        // header("location: index.php");
//     }
// }

?>
<?php include_once "header.php"; ?>

<style>

</style>

<body>
    <!-- <div class="toast">
        <div class="content">
            <i class="fa-solid fa-bell check"></i>

            <div class="message">
                <span class="text text-1">Notification!</span>
                <span class="text text-2"></span>
            </div>
            <i class="fa-solid fa-xmark close"></i>

            <div class="progress"></div>

        </div>
    </div> -->
    <div class="container">
        <div class="wrapper">
            <section class="users">
                <!-- Header -->
                <header>

                    <!-- Content -->
                    <div class="content">
                        <?php

                        $sql = "SELECT unique_id, fullname, img FROM users WHERE unique_id = '{$_SESSION['unique_id']}'";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            $row = $result->fetch_assoc();
                        }
                        ?>
                        <img src="upload/<?php echo $row['img']; ?>" alt="">
                        <div class="details">
                            <span><?php echo $row['fullname']; ?></span>
                            <p id="status-<?php echo $row['unique_id']; ?>"></p>
                        </div>

                    </div>
                    <a href="php/logout.php?logout_id=<?php echo $row['unique_id']; ?>" class="logout">Logout</a>
                </header>
                <!-- Search Box -->
                <div class="search">
                    <span class="text">Select an user to start chat</span>
                    <input type="text" placeholder="Enter name to search...">
                    <button><i class="fas fa-search"></i></button>
                </div>
                <!-- Users List -->
                <div class="users-list">
                </div>

            </section>
        </div>
    </div>

</body>
<script>
    const CURRENT_USER_ID = "<?php echo $_SESSION['unique_id']; ?>";
</script>
<script src="javascript/users.js"></script>

</html>
<?php

use App\User;

session_start();
require_once __DIR__ . '/vendor/autoload.php';

if (!isset($_SESSION['unique_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_GET['user_id'] ?? '';

if (!$user_id) {
    header("location: users.php");
    exit;
}

$user = User::find($user_id);

if (!$user) {
    header("location: users.php");
    exit;
}

$outgoing_id = $user_id;
$incoming_id = $_SESSION['unique_id'];
$online = ($user['status'] == 'Active') ? 'online' : '';
?>

<?php include_once "header.php"; ?>

<body>
    <div class="container">
        <div class="wrapper">
            <section class="chat-area">
                <!-- Header -->
                <header>
                    <!-- Content -->
                    <a href="users.php" class="back-icon"><i class="fas fa-arrow-left"></i></a>
                    <img src="upload/<?= $user['img'];  ?>" alt="" class="img <?= $online; ?>" id="status-<?= $outgoing_id; ?>">
                    <div class="details">
                        <span><?= $user['fullname']; ?></span>
                        <p id="user-status-<?= $outgoing_id; ?>"><?= $user['status']; ?> </p>
                    </div>
                </header>
                <div class="chat-box">

                </div>

                <form class="typing-area">
                    <input type="hidden" name="outgoing_id" id="out" value="<?= $incoming_id; ?>" hidden>
                    <input type="hidden" name="incoming_id" id="in" value="<?= $outgoing_id; ?>" hidden>
                    <input type="text" name="message" class="input-field" placeholder="Type a message here..." autocomplete="off" autofocus>
                    <button><i class='bx bxs-send'></i></button>

                </form>

            </section>
        </div>
    </div>
    <script src="javascript/chat.js"></script>

</body>


</html>
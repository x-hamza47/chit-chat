<?php

use App\User;

session_start();
require_once __DIR__ . '/vendor/autoload.php';

if (!isset($_SESSION['unique_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['unique_id'];

$user = User::find($user_id);

if (!$user) {
    echo "No user found!";
    exit;
}

?>

<?php include_once "header.php"; ?>

<body>

    <div class="container">
        <div class="wrapper">
            <section class="users">
                <!-- Header -->
                <header>
                    <!-- Content -->
                    <div class="content">
                        <img src="upload/<?= $user['img']; ?>" alt="">
                        <div class="details">
                            <span><?= $user['fullname']; ?></span>
                            <p id="status-<?= $user['unique_id']; ?>"><?= $user['status'] ?></p>
                        </div>
                    </div>
                    <a href="php/logout.php?logout_id=<?= $user['unique_id']; ?>" class="logout">Logout</a>
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
    const CURRENT_USER_ID = "<?= $_SESSION['unique_id']; ?>";
</script>
<script src="javascript/users.js"></script>

</html>
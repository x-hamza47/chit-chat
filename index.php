<?php
session_start();
if (isset($_SESSION['unique_id'])) {
    header("Location: users.php");
}
include "header.php";
?>

<body>
    <div class="container">
        <div class="wrapper">
            <section class="form login">
                <!-- HEADER -->
                <header>Hacker's Chat</header>
                <!-- Form Start -->
                <form>
                    <div class="error-txt">`</div>

                    <!-- Username -->
                    <div class="field input">
                        <label>Username</label>
                        <input type="text" name="uname" placeholder="Enter your username">
                    </div>
                    <!-- Password -->
                    <div class="field input">
                        <label>Password</label>
                        <input type="password" name="pass" placeholder="Enter your password" autocomplete="off">
                        <i class="fa-solid fa-eye show-ico"></i>
                    </div>

                    <!-- Submit -->
                    <div class="field sub-btn">
                        <input type="submit" value="Continue to Chat">
                    </div>


                </form>
                <!--Form End-->
                <div class="link">Not yet signed up? <a href="signup.php">Signup now </a></div>
            </section>
        </div>
    </div>

</body>
<script src="javascript/show-hide.js"></script>
<script type="module" src="javascript/auth-handler.js"></script>

</html>
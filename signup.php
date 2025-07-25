<?php
session_start();
if (isset($_SESSION['unique_id'])) {
    header("Location: users.php");
}
include "header.php";
?>

<body>
    <div class="container" style="font-weight: 500;">
        <div class="wrapper">
            <section class="form signup">
                <!-- HEADER -->
                <header>Hacker's Chat</header>
                <!-- Form Start -->
                <form action="" enctype="multipart/form-data">
                    <div class="error-txt"></div>
                    <!-- Profile-pic -->
                    <div class=" image profile-wrapper">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/7/7c/Profile_avatar_placeholder_large.png?20150327203541" id="preview" class="profile-image" alt="Profile" />
                        <label for="fileInput" class="edit-icon" title="Upload Pic">
                            <i class='bx bxs-pencil'></i>
                        </label>
                    </div>
                    <input type="file" id="fileInput" accept="image/*" name="image" />
                    <!-- profile pic end -->
                    <!-- Name  -->
                    <div class="field input">
                        <label>Full Name</label>
                        <input type="text" name="fullname" placeholder="Enter your name" autocomplete="off">
                    </div>
                    <!-- Email -->
                    <div class="field input">
                        <label>User Name</label>
                        <input type="text" name="uname" placeholder="Enter your username" autocomplete="off">
                    </div>
                    <!-- Password -->
                    <div class="field input">
                        <label>Password</label>
                        <input type="password" name="pass" placeholder="Enter new password">
                        <i class="fa-solid fa-eye show-ico"></i>
                    </div>
                    <!-- Submit -->
                    <div class="field sub-btn">
                        <input type="submit" name="save" value="Continue to Chat">
                    </div>


                </form>
                <!--Form End-->
                <div class="link">Already signed up? <a href="index.php">Login now </a></div>
            </section>
        </div>
    </div>

    <script>
        const fileInput = document.getElementById('fileInput');
        const preview = document.getElementById('preview');

        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
<!-- <script src="javascript/Auth.js"></script> -->
<script src="javascript/show-hide.js"></script>
<script type="module" src="javascript/auth-handler.js"></script>

</html>
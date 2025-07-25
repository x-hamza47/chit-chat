import Auth from './modules/Auth.js'

$(document).ready(function(){

     let isLogin = $(".login form").length > 0;
     let isSignup = $(".signup form").length > 0;

     if (isLogin) {
       new Auth({
         form: ".login form",
         sub_btn: ".sub-btn input",
         err_bx: ".error-txt",
         file_url: "php/login.php",
         redirect: "users.php",
       });
     }

     if (isSignup) {
       new Auth({
         form: ".signup form",
         sub_btn: ".sub-btn input",
         err_bx: ".error-txt",
         file_url: "php/signup.php",
         redirect: "index.php",
       });
     }

});



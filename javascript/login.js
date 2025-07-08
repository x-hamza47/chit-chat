$(document).ready(function(){

    const login_form = $('.login form'), 
    login_continueBtn = $('.sub-btn input'),
    login_errorTxt = $('.error-txt');


    login_form.submit((e) => {
        e.preventDefault();
    });
    
    login_continueBtn.click(() => {
        const LoginformData = new FormData(login_form[0]);
        
        $.ajax({
            url : "php/login.php",
            type : "POST",
            data : LoginformData,
            contentType: false, 
            processData: false,
            success : function(data){
                if (data === "success") {
                        location.href = "users.php";
                    }else{
                        login_errorTxt.text(data);
                        login_errorTxt.css("display", "block");
                    }
            }
        })
    });


});



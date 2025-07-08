$(document).ready(function(){

    const form = $('.signup form'), 
    continueBtn = $('.sub-btn input'), 
    errorTxt = $('.error-txt');

    form.submit((e) => {
        e.preventDefault();
    });
    
    continueBtn.click(() => {
        const formData = new FormData(form[0]);
        
        $.ajax({
            url : "php/signup.php",
            type : "POST",
            data : formData,
            contentType: false, 
            processData: false,
            success : function(data){
                    if (data === "Success") {
                        location.href = "index.php";
                        console.log(data);
                    }else{
                        console.log(data);
                        errorTxt.text(data);
                        errorTxt.css("display", "block");
                    }
            }
        })
    });


});



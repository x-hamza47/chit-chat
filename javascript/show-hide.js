const passField = $(".form .field input[type='password']"),
eyebtn = $(".form .field i");

eyebtn.click = () =>{
    if(passField.type == 'password'){
        eyebtn.classList.replace('fa-eye', 'fa-eye-slash');
        passField.type = "text";
    }else{
        passField.type = "password";
        eyebtn.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
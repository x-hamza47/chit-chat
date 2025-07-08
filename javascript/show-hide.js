const passField = document.querySelector(".form .field input[type='password']"),
eyebtn = document.querySelector(".form .field i");

eyebtn.onclick = () =>{
    if(passField.type == 'password'){
        eyebtn.classList.replace('fa-eye' ,'fa-eye-slash');
        passField.type = "text";
    }else{
        passField.type = "password";
        eyebtn.classList.replace('fa-eye-slash' ,'fa-eye');
    }
}
const passField = $(".form .field input[type='password']"),
eyebtn = $(".form .field .show-ico");

eyebtn.click(() => {

  if (passField.attr("type") === "password") {
    eyebtn.removeClass("fa-eye").addClass("fa-eye-slash");
    passField.attr("type", "text");
  } else {
    passField.attr("type", "password");
    eyebtn.removeClass("fa-eye-slash").addClass("fa-eye");
  }
});

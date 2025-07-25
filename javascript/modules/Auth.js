// Info: Authentication Js

export default class Auth {
    
  constructor({form, sub_btn, err_bx, file_url, redirect}) {
    this.form = $(form);
    this.sub_btn = $(sub_btn);
    this.err_bx = $(err_bx);
    this.file_url = file_url;
    this.redirect = redirect;
    this.init();
  }

  init() {
    this.form.submit((e) => e.preventDefault());
    this.sub_btn.click(() => this.handleSubmit());
  }

  handleSubmit() {
    const formData = new FormData(this.form[0]);

    $.post({
      url: this.file_url,
      data: formData,
      contentType: false,
      processData: false,
      success: (response) => this.handleResponse(response),
    });
  }

  handleResponse(response) {
    let data;
    try {
      data = JSON.parse(response);
    } catch (e) {
      data = { status: response === "success", message: response };
    }

    if (data.status) {
        location.href = this.redirect;
    } else {
        this.err_bx.text(data.message || "Something went wrong!");
        this.err_bx.show();

        setTimeout(() => this.err_bx.fadeOut(), 3000);
    }
  }
}

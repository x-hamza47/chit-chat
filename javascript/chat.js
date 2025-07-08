$(document).ready(function () {
  
  const chat_form = $(".typing-area"),
  input_field = $(".input-field"),
  chat_bx = $(".chat-box");
  chat_sendBtn = $("button");
  
  chat_form.submit((e) => {
    e.preventDefault();
  });
  
  const in_go = $("#in").val(),
  out_go = $("#out").val();
  
  $.ajax({
    url: "php/get-chat.php",
    type: "POST",
    data: { inco_id: in_go, outgo_id: out_go },
    success: function (data) {
      chat_bx.html(data);
      // if (!chat_bx.hasClass("active")) {
        scrollDown();
      // }
    },
  });
  //!  Connect to Workerman WebSocket server
  const socket = new WebSocket("ws://192.168.1.108:2346");

  socket.onopen = () => {
    console.log("✅ Connected to server");

    socket.send(
      JSON.stringify({
        type: "init",
        user_id: parseInt(out_go),
      })
    );
  };

  socket.onmessage = (e) => {
    const data = JSON.parse(e.data);

    if (data.type === "message") {
      const isWindowFocused = document.hasFocus();
      let msgHTML = "";

      if (data.from == out_go) {
        //! Outgoing message
        msgHTML = `
            <div class="chat outgoing">
              <div class="details">
                <p>${data.message}</p>
              </div>
              </div>
          `;
      } else {
        //! Incoming message
        msgHTML = `
            <div class="chat incoming">
              <div class="details">
                <p>${data.message}</p>
              </div>
            </div>
          `;
      }
      chat_bx.append(msgHTML);
      scrollDown();

      // Todo: Notification
      if (!isWindowFocused) {
        const notification = new Notification("🔔 New message", {
          body: data.message,
          icon: "./img/android-chrome-192x192.png",
        });

        setTimeout(() => {
          notification.close();
        }, 5000);
      }

    }
  };

  chat_sendBtn.click(() => {
    const message = input_field.val().trim();
    if (!message) return;

    const msgData = {
      type: "message",
      from: parseInt(out_go),
      to: parseInt(in_go),
      message: message,
    };

    socket.send(JSON.stringify(msgData));

    input_field.val("");
    scrollDown();
  });

  // $(".chat-area").mouseenter(() => {
  //   chat_bx.addClass("active");
  // });
  // $(".chat-area").mouseleave(() => {
  //   chat_bx.removeClass("active");
  // });

  function scrollDown() {
    chat_bx.scrollTop(chat_bx[0].scrollHeight);
  }
});

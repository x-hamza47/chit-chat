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
  const socket = new WebSocket("ws://192.168.1.109:2346");

  socket.onopen = () => {
    console.log("✅ Connected to server");

    socket.send(
      JSON.stringify({
        type: "init",
        user_id: parseInt(out_go),
      })
    );

    sendChatFocusStatus(true);
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

    if (data.type === "typing") {
      let status_bx = $("#user-status");
      status_bx.text("typing...").addClass('typing');
      // if ($("#typing-indicator").length === 0) {

      scrollDown();
      // }
    }

    if (data.type === "stop_typing") {
      let status_bx = $("#user-status");
      status_bx.text("Active").removeClass('typing'); 
    }

  };

  function sendChatFocusStatus(isFocused) {
    socket.send(
      JSON.stringify({
        type: "chat_focus",
        user_id: parseInt(out_go),
        chatting_with: isFocused ? parseInt(in_go) : null,
      })
    );
  }


  window.addEventListener("focus", () => {
    $.post("php/update-read.php", {
      incoming_id: in_go,
      outgoing_id: out_go,
    });
    sendChatFocusStatus(true)
  });
  window.addEventListener("blur", () => sendChatFocusStatus(false));

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
    // 👇 Trigger users list update in other clients
    socket.send(
      JSON.stringify({
        type: "new_message",
        from: parseInt(out_go),
        to: parseInt(in_go),
      })
    );
    input_field.val("");
    scrollDown();
  });

  // ! Typing Indicator
  let typingTimeout;

  input_field.on("input", () => {
    socket.send(
      JSON.stringify({
        type: "typing",
        from: parseInt(out_go),
        to: parseInt(in_go),
      })
    );

    clearTimeout(typingTimeout);
    typingTimeout = setTimeout(() => {
      socket.send(
        JSON.stringify({
          type: "stop_typing",
          from: parseInt(out_go),
          to: parseInt(in_go),
        })
      );
    }, 1000); 
  });// ! Typin indication end

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


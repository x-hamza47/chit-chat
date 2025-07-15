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

  //!  Connect to Workerman WebSocket server
  const socket = new WebSocket("ws://192.168.1.106:2346");
  $.ajax({
    url: "php/get-chat.php",
    type: "POST",
    data: { inco_id: in_go, outgo_id: out_go },
    success: function (data) {
      chat_bx.html(data);
      // if (!chat_bx.hasClass("active")) {
      scrollDown();
      // }

      socket.send(
        JSON.stringify({
          type: "mark_read",
          from: parseInt(in_go), 
          to: parseInt(out_go), 
        })
      );

    },
  });

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

      if (
        (data.from == in_go && data.to == out_go) ||
        (data.from == out_go && data.to == in_go)
      ) {
        let msgHTML = "";

        if (data.from == out_go) {
          //! Outgoing message
          msgHTML = `
            <div class="chat outgoing">
              <div class="details">
                <p>${data.message}</p>
                <div class="meta">
                  <span class="time">${data.time}</span>
                  <span class="tick-icon">
                      <svg width="15" height="15" viewBox="3 0 25 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M4 12L8 16L20 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                          <path d="M11 12L15 16L27 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                  </span>
                </div>
              </div>
              </div>
          `;
        } else {
          //! Incoming message
          msgHTML = `
            <div class="chat incoming">
              <div class="details">
                <p>${data.message}</p>
                  <div class="meta">
                  <span class="time">${data.time}</span>
                </div>
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
    }

    if (
      data.type == "read_update" &&
      data.from == in_go &&
      data.to == out_go
    ) {
        console.log("📥 Read update received:", data);
      console.log("hello wordl");
      
      $(".tick-icon").addClass("readed");
    }
    if (data.type === "status_update") {
      updateUserStatus(data.user_id, data.status);
    }

    if (data.type === "typing") {
      let status_bx = $(`#user-status-${data.from}`);
      status_bx.text("typing...").addClass("typing");
      // if ($("#typing-indicator").length === 0) {
      scrollDown();
      // }
    }

    if (data.type === "stop_typing") {
      let status_bx = $(`#user-status-${data.from}`);
      status_bx.text("Active").removeClass("typing");
    }
  };

  function updateUserStatus(userId, status) {
    const el = $(`#status-${userId}`);
    if (el.length === 0) return;

    const isOffline = status.toLowerCase() == "offline";

    if (isOffline) {
      $(`#user-status-${userId}`).text(status);
      el.removeClass("online");
    } else {
      $(`#user-status-${userId}`).text(status);
      el.addClass("online");
    }
  }

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
    // $.post("php/update-read.php", {
    //   incoming_id: in_go,
    //   outgoing_id: out_go,
    // });

    // socket.send(
    //   JSON.stringify({
    //     type: "mark_read",
    //     from: parseInt(in_go),
    //     to: parseInt(out_go),
    //   })
    // );

    sendChatFocusStatus(true);
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
      // time: new Date().toISOString()
    };

    socket.send(JSON.stringify(msgData));
    //  Trigger users list update in other clients
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
  }); // ! Typin indication end

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

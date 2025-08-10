import ChatSocket from "./modules/ChatSocket.js";
import { appendMessage, updateUserStatus, scrollDown } from "./utils/chat-ui.js";

$(document).ready(function () {
  const chat_form = $(".typing-area");
  const input_field = $(".input-field");
  const chat_bx = $(".chat-box");
  const chat_sendBtn = $("button");
  const in_go = $("#in").val(); //* Chatting with
  const out_go = $("#out").val(); //* Current User (you)

    chat_form.submit((e) => {
      e.preventDefault();
    });
  const socket = new ChatSocket({
    url: "ws://192.168.1.110:2346",
    user_id: out_go,
  });

  $.ajax({
    url: "php/get-chat.php",
    type: "POST",
    data: { inco_id: in_go },
    success: function (data) {
      chat_bx.html(data);

      // if (!chat_bx.hasClass("active")) {
      scrollDown();
      // }
      socket.send({
        type: "mark_read",
        from: out_go,
        to: in_go,
      });
    },
  });

  // ! Open
  socket.on("open", () => {
    socket.send({
      type: "chat_focus",
      user_id: out_go,
      chatting_with: in_go,
    });
  });

  // ! Message
  socket.on("message", (data) => {
    if (
      (data.from == in_go && data.to == out_go) ||
      (data.from == out_go && data.to == in_go)
    ) {
      appendMessage(data, out_go);
    }
  });

  socket.on("typing", (data) => {
    $(`#user-status-${data.from}`).text("typing...").addClass("typing");
  });

  socket.on("stop_typing", (data) => {
    $(`#user-status-${data.from}`).text("Active").removeClass("typing");
  });

  socket.on("read_update", (data) => {
    $(".tick-icon").addClass("readed");
  });

  socket.on("status_update", (data) => {
    updateUserStatus(data.user_id, data.status);
  });

  // ! Send Message
  chat_sendBtn.click(() => {
    const message = input_field.val().trim();
    if (!message) return;

    socket.send({
      type: "message",
      from: out_go,
      to: in_go,
      message: message,
    });
    socket.send({
      type: "new_message",
      from: out_go,
      to: in_go,
    });
    input_field.val("");
    scrollDown();
  });

  // ! Typing Indicator
  let typingTimeout;

  input_field.on("input", () => {
    socket.send({
        type: "typing",
        from: out_go,
        to: in_go,
    });

    clearTimeout(typingTimeout);
    typingTimeout = setTimeout(() => {
      socket.send({
          type: "stop_typing",
          from: out_go,
          to: in_go,
        });
    }, 1000);
  }); // ! Typin indication end

});

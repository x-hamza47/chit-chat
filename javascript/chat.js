import ChatSocket from "./modules/ChatSocket.js";
import {
  appendMessage,
  updateUserStatus,
  scrollDown,
} from "./utils/chat-ui.js";

$(document).ready(function () {
  const chat_form = $(".typing-area");
  const input_field = $(".input-field");
  const chat_bx = $(".chat-box");
  const chat_sendBtn = $("button");

  const current_user_id = $("#current-user").val(); // You
  const other_user_id = $("#other-user").val(); // Person you're chatting with

  chat_form.submit((e) => {
    e.preventDefault();
  });
  const wsProtocol = location.protocol === "https:" ? "wss" : "ws";

  const socket = new ChatSocket({
    url: `${wsProtocol}://${location.host}`,
    user_id: current_user_id,
  });

  // const socket = new ChatSocket({
  //   url: "ws://localhost:2346",
  //   user_id: current_user_id,
  // });

  $.ajax({
    url: "php/get-chat.php",
    type: "POST",
    data: { other_user_id: other_user_id },
    success: function (data) {
      chat_bx.html(data);
      scrollDown();

      socket.send({
        type: "mark_read",
        from: current_user_id,
        to: other_user_id,
      });
    },
  });

  // ! Open
  socket.on("open", () => {
    socket.send({
      type: "chat_focus",
      user_id: current_user_id,
      chatting_with: other_user_id,
    });
  });

  // ! Message
  socket.on("message", (data) => {
    if (
      (data.from == other_user_id && data.to == current_user_id) ||
      (data.from == current_user_id && data.to == other_user_id)
    ) {
      appendMessage(data, current_user_id);
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
      from: current_user_id,
      to: other_user_id,
      message: message,
    });
    socket.send({
      type: "new_message",
      from: current_user_id,
      to: other_user_id,
    });
    input_field.val("");
    scrollDown();
  });

  // ! Typing Indicator
  let typingTimeout;

  input_field.on("input", () => {
    socket.send({
      type: "typing",
      from: current_user_id,
      to: other_user_id,
    });

    clearTimeout(typingTimeout);
    typingTimeout = setTimeout(() => {
      socket.send({
        type: "stop_typing",
        from: current_user_id,
        to: other_user_id,
      });
    }, 1000);
  });
});

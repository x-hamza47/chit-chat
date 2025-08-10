$(document).ready(function () {
  if (Notification.permission !== "granted") {
    Notification.requestPermission();
  }

  const socket = new WebSocket("ws://192.168.1.110:2346");

  socket.onopen = function () {
    console.log("✅ Connected to server");

    socket.send(
      JSON.stringify({
        type: "init",
        user_id: CURRENT_USER_ID,
      })
    );
  };

  socket.onmessage = function (event) {
    const data = JSON.parse(event.data);

    if (data.type === "new_message") {
      usersList();
    }

    if (data.type === "status_update") {
      // setTimeout(() => {
        updateUserStatus(data.user_id, data.status);
      // }, 1000);
    }

    // !typin indicators
    if (data.type === "typing") {
      $(`#lastmsg-${data.from}`).hide();
      $(`#typing-${data.from}`).show();
    }

    if (data.type === "stop_typing") {
      $(`#typing-${data.from}`).hide();
      $(`#lastmsg-${data.from}`).show();
    }
    //!typing indicators end

    if (data.type == "read_update") {
      console.log("📥 Read update received:", data);
      $(`#lastmsg-${data.from} .tick-icon`).addClass("readed");
    }
  };

  function updateUserStatus(userId, status) {
    const el = $(`#status-${userId}`);
    if (el.length === 0) return;

    if (userId == CURRENT_USER_ID) {
      el.text(status);
    }

    const isOffline = status.toLowerCase() === "offline";

    if (isOffline) {
      el.removeClass("online");
    } else {
      el.addClass("online");
    }
  }

  const usersList = () => {
    $.ajax({
      url: "php/users.php",
      type: "GET",
      success: function (data) {
        if (!$(search_bar).hasClass("active")) {
          $(".users-list").html(data);
        }
      },
    });
  };
  usersList();

});

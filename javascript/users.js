const search_bar = document.querySelector(".users .search input"),
  search_btn = document.querySelector(".users .search button");

search_btn.addEventListener("click", () => {
  search_bar.classList.toggle("active");
  search_bar.focus();
  search_btn.classList.toggle("active");

  if (search_btn.firstChild.classList.contains("fa-search")) {
    search_btn.firstChild.classList.replace("fa-search", "fa-xmark");
  } else {
    search_btn.firstChild.classList.replace("fa-xmark", "fa-search");
  }
});

$(document).ready(function () {
  if (Notification.permission !== "granted") { 
    Notification.requestPermission();
  }

  const socket = new WebSocket("ws://192.168.1.106:2346");

  socket.onopen = function () {
    console.log("✅ Connected to server");

    socket.send(
      JSON.stringify({
        type: "init",
        user_id: parseInt(CURRENT_USER_ID),
      })
    );
  };

  socket.onmessage = function (event) {
    const data = JSON.parse(event.data);
    
    if (data.type === "new_message") {
      usersList();
    }
    
    if (data.type === "status_update") {
      setTimeout(() => {
        updateUserStatus(data.user_id, data.status);

      }, 1000);
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
  };

  function updateUserStatus(userId, status) {
    const el = $(`#status-${userId}`);
    if (el.length === 0) return;

    if(userId == CURRENT_USER_ID){
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
  }
usersList();
  $(search_bar).keyup(function () {
    let search_term = $(search_bar).val();

    if (search_term != "") {
      $(search_bar).addClass("active");
    } else {
      $(search_bar).removeClass("active");
      $(search_btn).removeClass("active");
      $(search_btn)
        .find(":first-child")
        .removeClass("fa-xmark")
        .addClass("fa-search");
    }
    $.ajax({
      url: "php/search.php",
      type: "POST",
      data: { search: search_term },
      contentType: "application/x-www-form-urlencoded",
      success: function (data) {
        $(".users-list").html(data);
      },
    });
  }); //search bar ajax
});

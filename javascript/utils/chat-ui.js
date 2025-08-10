const chat_bx = $(".chat-box");
export function appendMessage(data, current_user) {
  const msgHTML =
    data.from == current_user
      ? `<div class="chat outgoing">
              <div class="details">
                <p>${data.message}</p>
                <div class="meta">
                  <span class="time">${data.time}</span>
                  <span class="tick-icon">
                      <svg width="15" height="15" viewBox="3 0 25 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M4 12L8 16L20 4" stroke="#ccc" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                          <path d="M11 12L15 16L27 4" stroke="#ccc" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                  </span>
                </div>
              </div>
              </div>`
      : `<div class="chat incoming">
                <div class="details">
                    <p>${data.message}</p>
                    <div class="meta">
                    <span class="time">${data.time}</span>
                    </div>
                </div>
            </div>`;

  chat_bx.append(msgHTML);
  scrollDown();
}

export function updateUserStatus(userId, status) {
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

export function scrollDown() {
  chat_bx.scrollTop(chat_bx[0].scrollHeight);
}

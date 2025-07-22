<?php

use App\Message;
use App\User;

session_start();
require_once __DIR__ . '/../vendor/autoload.php';

if (!isset($_SESSION['unique_id'])) {
    header("Location: ../index.php");
    exit;
}

$outgoing_id = $_SESSION['unique_id'];
$output = "";

$users = User::getUsers($outgoing_id);

if (empty($users)) {
    $output .= "No users are available to chat";
} else {
    foreach ($users as $user) {
        $message = Message::forUser($outgoing_id)->withUser($user['unique_id']);
        $last_msg_data = $message->orderBy('DESC')->lastMessage()->get();

        $last_msg = $last_msg_data['msg'] ?? "No messages available";
        $msg = (strlen($last_msg) > 24) ?  substr($last_msg, 0, 24) . "..." : $last_msg;

        // ! tick icon
        $you = '';
        if (!empty($last_msg_data) && $last_msg_data['outgoing_msg_id'] === $outgoing_id) {

            $is_read = $message->isMessageRead($last_msg_data['id']);
            $tickColor = $is_read ? '#4fa3f7' : '#ccc';

            $you = '<span class="tick-icon">
                    <svg width="15" height="15" viewBox="3 0 25 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 12L8 16L20 4" stroke="' . $tickColor . '" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M11 12L15 16L27 4" stroke="' . $tickColor . '" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>';
        }

        $time = isset($last_msg_data['created_at']) ? date('h:i A', strtotime($last_msg_data['created_at'])) : '';
        $unread_count = $message->unreadCount();
        // Info: Notification Dot/Counter
        $notification_dot = ($unread_count > 0) ?
            "<div class='notification-dot'>
            <div class='meta'>
                <span class='time'>" . $time . "</span>
            </div>
            " . ($unread_count > 99 ? '99+' : $unread_count) . "
            </div>"
            : '';

        $online = ($user['status'] == "Active") ? "online" : "";

        $output .=  '<a href="chat.php?user_id=' . $user['unique_id'] . '">
                <div class="content">
                <img src="upload/' . $user['img'] . '" alt="" class="list-imgs ' . $online . '" id="status-' . $user['unique_id'] . '">
                <div class="details">
                    <span>' . $user['fullname'] . '</span>
                    <p id="lastmsg-' . $user['unique_id'] . '">' . $you . $msg . '</p> 
                    <div class="typing" id="typing-' . $user['unique_id'] . '" style="display: none;">typing...</div>
                </div>
                </div>
                ' . $notification_dot . '
                </a>';
    }
}

echo $output;

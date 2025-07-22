<?php

use App\Message;

require_once __DIR__ . '/../vendor/autoload.php';

session_start();

if (!isset($_SESSION['unique_id'])) {
    header("Location: ../index.php");
    exit;
}

$outgoing_id = $_SESSION['unique_id'];
$incoming_id = $_POST['inco_id'] ?? null;

if (!$incoming_id) {
    echo "No user selected";
    exit;
}

$messages = Message::forUser($outgoing_id)
    ->withUser($incoming_id)
    ->get();

$output = "";
$unread = false;

// ! Printing Chats
foreach ($messages as $msg) {
    $time = date('h:i A', strtotime($msg['created_at']));
    $readed = ($msg['is_read']) ? '#4fa6f7ff' : '#ccc';

    if ($msg['outgoing_msg_id'] == $outgoing_id) {
        $output .= '<div class="chat outgoing">
                        <div class="details">
                            <p>' . htmlspecialchars($msg['msg']) . '</p>
                            <div class="meta">
                                <span class="time">' . $time . '</span>
                                <span class="tick-icon ' . ($readed ? 'readed': '') . '">
                                    <svg width="15" height="15" viewBox="3 0 25 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M4 12L8 16L20 4" stroke="' . $readed . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M11 12L15 16L27 4" stroke="' . $readed . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>';
    } else {
        if (!$msg['is_read'] && !$unread) {
            $output .= '<div class="new_msg_badge">
                            <span>new messages</span>
                        </div>';
            $unread = true;
        }

        $output .= '<div class="chat incoming">
                        <div class="details">
                            <p>' . htmlspecialchars($msg['msg']) . '</p>
                            <div class="meta">
                                <span class="time">' . $time . '</span>
                            </div>
                        </div>
                    </div>';
    }
}
echo $output;

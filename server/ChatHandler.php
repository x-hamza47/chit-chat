<?php

use App\Message;
use App\User;

require_once __DIR__ . '/BaseHandler.php';

class ChatHandler extends BaseHandler
{

    public function onMessage($conn, $data)
    {
        $decoded = json_decode($data, true);

        if (!$decoded || !isset($decoded['type'])) return;

        switch ($decoded['type']) {

            case 'init':
                $this->addConnection($decoded['user_id'], $conn);
                User::updateStatus($decoded['user_id'], 'Active');
                $this->broadcastStatus($decoded['user_id'], 'Active');
                break;

            case 'message':
                $this->saveAndSendMessage($decoded['from'], $decoded['to'], $decoded['message']);
                break;

            case 'mark_read':
                //
                $this->markMessagesAsRead($decoded['from'], $decoded['to']);
                // $this->notifySenderRead($decoded['from'], $decoded['to']);
                break;
                
            case 'new_message':
                $this->broadcastNewMessageNotice($decoded['from'], $decoded['to']);
                break;

            case 'chat_focus':
                $this->setActiveChat($decoded['user_id'], $decoded['chatting_with'] ?? null);
                break;
                
            case 'typing':
            case 'stop_typing':
                $this->sendToUser($decoded['to'], $decoded);
                break;
        }
    }

    private function saveAndSendMessage($from, $to, $message)
    {
        date_default_timezone_set('Asia/Karachi');
        $date = date('h:i A');
        $is_read = (isset($this->activeChats[$to]) && $this->activeChats[$to] == $from) ? 1 : 0;

        // ! Store message in database
        Message::forUser($from)->withUser($to)->storeMessage($message, $is_read);

        // ! Send message to user
        $messagePayload = json_encode([
            "type" => "message",
            "from" => $from,
            "to" => $to,
            "message" => $message,
            "time" => $date,
        ]);

        foreach ([$from, $to] as $uid) {
            $conn = $this->getConnection($uid);
            if ($conn) $conn->send($messagePayload);
        }

        if ($is_read) {
            $this->notifySenderRead($to, $from);
        }
    }

    // ! Connection Close
    public function onClose($conn)
    {
        if (isset($conn->user_id)) {
            $user_id = $conn->user_id;

            $this->removeConnection($user_id); // * Removing connection
            User::updateStatus($user_id, 'Offline'); // * Update offline status
            $this->broadcastStatus($user_id, 'Offline'); // * Sending status to other users
            $this->setActiveChat($user_id, null);
        }
    }
}

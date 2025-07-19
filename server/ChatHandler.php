<?php

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
                $this->updateStatus($decoded['user_id'], 'Active');
                $this->broadcastStatus($decoded['user_id'], 'Active');
                break;

            case 'message':
                $this->sendMessage($decoded);
                if (isset($conn->user_id) && $conn->user_id == $decoded['from']) {
                    $this->saveMessage($decoded['from'], $decoded['to'], $decoded['message']);
                }
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


    // ! Send Message 
    private function sendMessage($data)
    {
        date_default_timezone_set('Asia/Karachi');
        $date = date('h:i A');
        $is_read = (isset($this->activeChats[$data['to']]) && $this->activeChats[$data['to']] == $data['from']);

        $messagePayload = json_encode([
            "type" => "message",
            "from" => $data['from'],
            "to" => $data['to'],
            "message" => $data['message'],
            "time" => $date,
        ]);

        foreach([$data['from'],$data['to']] as $uid){
            $conn = $this->getConnection($uid);
            if ($conn) $conn->send($messagePayload);
        }

       
        if ($is_read) {
            $this->notifySenderRead($data['to'], $data['from']); 
            // $this->notifySenderRead($data['from'], $data['to']); 
        }

    }

    // ! Store message in database
    private function saveMessage($from, $to, $message)
    {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "chat_app";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if (!$conn) {
            echo "Database Connection Error : " . $conn->connect_error;
        }

        $is_read = (isset($this->activeChats[$to]) && $this->activeChats[$to] == $from) ? 1 : 0;

        $sql = $conn->prepare("INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg, is_read)
        VALUES (?, ?, ?, ?)");
        $sql->bind_param("iisi", $to, $from, $message, $is_read);
        $sql->execute();
        $sql->close();
        $conn->close();
    }

    // ! Connection Close
    public function onClose($conn)
    {
        if (isset($conn->user_id)) {
            $user_id = $conn->user_id;

            $this->removeConnection($user_id); // * Removing connection
            $this->updateStatus($user_id, 'Offline'); // * Update offline status
            $this->broadcastStatus($user_id, 'Offline'); // * Sending status to other users
            $this->setActiveChat($user_id, null);
        }
    }
}

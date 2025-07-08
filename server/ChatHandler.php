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
        }
    }


    // ! Send Message 
    private function sendMessage($data)
    {
        $messagePayload = json_encode([
            "type" => "message",
            "from" => $data['from'],
            "to" => $data['to'],
            "message" => $data['message'],
        ]);

        foreach([$data['from'],$data['to']] as $uid){
            $conn = $this->getConnection($uid);
            if ($conn) $conn->send($messagePayload);
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

        $sql = $conn->prepare("INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg)
        VALUES (?, ?, ?)");
        $sql->bind_param("iis", $to, $from, $message);
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
        }
    }
}

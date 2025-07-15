<?php

class BaseHandler {

    protected $users = [];
    protected $activeChats = [];

    protected function updateStatus($user_id, $status){

        $conn = new mysqli('localhost', 'root', '', 'chat_app');
        $sql = $conn->prepare('UPDATE users SET status = ? WHERE unique_id = ?');
        $sql->bind_param("si",$status, $user_id);
        $sql->execute();
        $sql->close();
        $conn->close();
    }

    protected function broadcastStatus($user_id, $status){
        $payload = json_encode([
            'type' => 'status_update',
            'user_id' => $user_id,
            'status' => $status,
        ]);

        foreach($this->users as $uid => $conn){
            // if($uid != $user_id){
                $conn->send($payload);
            // }
        }
    }

    protected function markMessagesAsRead($readerId, $senderId)
    {
        if (!isset($this->activeChats[$readerId]) || $this->activeChats[$readerId] != $senderId) {
            return;
        }
        $conn = new mysqli('localhost', 'root', '', 'chat_app');

        $sql = $conn->prepare("UPDATE messages SET is_read = 1 
                           WHERE incoming_msg_id = ? AND outgoing_msg_id = ? AND is_read = false");
        $sql->bind_param("ii", $readerId, $senderId);
        $sql->execute();

        // if ($sql->affected_rows > 0) {
        //     $this->notifySenderRead($readerId, $senderId);
        // }
        $sql->close();
        $conn->close();

    }

    protected function notifySenderRead($readerId, $senderId)
    {
        $payload = json_encode([
            "type" => "read_update",
            "from" => $readerId,
            "to" => $senderId
        ]);

        $senderConn = $this->getConnection($senderId);
        if ($senderConn) {
            $senderConn->send($payload);
        }
    }
    
    protected function broadcastNewMessageNotice($from, $to)
    {
        $conn = $this->getConnection($to);
        if ($conn) {
            $conn->send(json_encode([
                'type' => 'new_message',
                'from' => $from
            ]));
        }
    }
    protected function setActiveChat($user_id, $chatting_with)
    {
        if ($chatting_with !== null) {
            $this->activeChats[$user_id] = $chatting_with;
        } else {
            unset($this->activeChats[$user_id]);
        }
    }

    protected function sendToUser($user_id, $payload){
        $conn = $this->getConnection($user_id);

        if ($conn){
            $conn->send(json_encode($payload));
        }
    }

    protected function addConnection($user_id, $conn)
    {
        $this->users[$user_id] = $conn;
        $conn->user_id = $user_id;
    }

    protected function removeConnection($user_id)
    {
        unset($this->users[$user_id]);
    }

    protected function getConnection($user_id)
    {
        return $this->users[$user_id] ?? null;
    }
}
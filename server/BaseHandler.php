<?php

class BaseHandler {

    protected $users = [];

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
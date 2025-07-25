<?php

namespace App;

use PDO;
use App\Traits\MessageHelper;
use PDOException;

class Message extends DB
{
    use MessageHelper;

    private $outgoing_id;
    private $incoming_id;


    public static function forUser(String $outgoing_id): self
    {
        $instance = new self();
        $instance->outgoing_id = $outgoing_id;
        return $instance;
    }

    public function withUser(String $incoming_id): self
    {
        $this->incoming_id = $incoming_id;
        return $this;
    }

    public function storeMessage(String $message, Int $is_read = 0): bool
    {
        try {
            $conn = parent::getConnection();
            $stmt = $conn->prepare("INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg, is_read)
                        VALUES (:in_id, :out_id, :msg, :read)");

            return $stmt->execute([
                'in_id' => $this->incoming_id,
                'out_id' => $this->outgoing_id,
                'msg' => $message,
                'read' => $is_read
            ]);

        } catch (PDOException $e) {

            error_log("Message save error: " . $e->getMessage());
            return false;
        }
    }

    public function get(): array|null
    {
        try {
            $conn = parent::getConnection();
            $limit = $this->last_msg ? "LIMIT 1" : "";

            $stmt = $conn->prepare("SELECT * FROM messages
                        WHERE (outgoing_msg_id = :out_id AND  incoming_msg_id = :in_id)
                            OR (outgoing_msg_id = :in_id AND  incoming_msg_id = :out_id)
                        ORDER BY created_at {$this->order} {$limit}");

            $stmt->execute([
                ':out_id' => $this->outgoing_id,
                ':in_id' => $this->incoming_id,
            ]);

            return $this->last_msg ?
                ($stmt->fetch(PDO::FETCH_ASSOC) ?: null) :
                $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return $this->last_msg ? null : [];
        }
    }

    public function markAsRead(): bool
    {
        $conn = parent::getConnection();
        $stmt = $conn->prepare("UPDATE messages SET is_read = 1 
                           WHERE incoming_msg_id = :out_id AND outgoing_msg_id = :in_id 
                           AND is_read = false");

        return $stmt->execute([
            ':out_id' => $this->outgoing_id,
            ':in_id' => $this->incoming_id
        ]);
    }

    public function unreadCount(): int
    {
        try {
            $conn = parent::getConnection();
            $stmt = $conn->prepare("SELECT COUNT(id) AS unread FROM messages 
                        WHERE incoming_msg_id = :out_id AND outgoing_msg_id = :in_id 
                        AND is_read = false");
            $stmt->execute([
                ':out_id' => $this->outgoing_id,
                ':in_id' => $this->incoming_id
            ]);

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['unread'] ?? 0;
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function isMessageRead(Int $id): bool
    {
        $conn = parent::getConnection();
        $stmt = $conn->prepare("SELECT is_read FROM messages WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['is_read'] ?? false;
    }
}

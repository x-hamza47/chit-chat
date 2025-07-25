<?php

namespace App;

use PDO;
use PDOException;
use Ramsey\Uuid\Uuid;
use App\Traits\UserHelper;
use App\Traits\UserValidator;
use App\Utils\ImageUploader;

class User extends DB
{
    use UserHelper, UserValidator;

    // !login user->
    public static function login(String $uname, String $password)
    {

        $validation = self::validateLogin($uname, $password);
        if (!$validation["status"]) {
            return $validation;
        }

        try {
            $conn = parent::getConnection();
            $stmt = $conn->prepare("SELECT * FROM users WHERE uname = :uname");
            $stmt->execute([":uname" => $uname]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                self::updateStatus($user['unique_id'], "Active"); //* Update status

                $_SESSION['unique_id'] = $user['unique_id'];
                $_SESSION['last_online'] = time();

                return ['status' => true, 'message' => 'success'];
            } else {
                return ['status' => false, 'message' => 'Username or Password is Incorrect'];
            }
        } catch (PDOException $e) {
            return ['status' => false, 'message' => 'Login error: ' . $e->getMessage()];
        }
    }

    // !Register user->
    public static function register(array $data)
    {
        $conn = parent::getConnection();

        $fullname = trim($data['fullname'] ?? '');
        $uname = trim($data['uname'] ?? '');
        $pass = trim($data['pass'] ?? '');

        $validation = self::validateRegister($fullname, $uname, $pass);
        if (!$validation['status']) {
            return $validation;
        } // !Return validation errors

        // Info: Image Uploading
        $image_upload = ImageUploader::upload($_FILES['image']);
        if (!$image_upload['status']) {
            return $image_upload;
        }
        $new_name = $image_upload['filename'];

        try {
            $random_id = Uuid::uuid4()->toString();
            $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (unique_id, fullname, uname, password, img)  VALUES ( :id, :fullname, :uname, :pass, :img )");

            $stmt->execute([
                ":id" => $random_id,
                ":fullname" => $fullname,
                ":uname" => $uname,
                ":pass" => $hashed_pass,
                ":img" => $new_name
            ]);
            return ['status' => true, 'message' => 'Success'];
        } catch (PDOException $e) {
            ImageUploader::delete($new_name);
            return ['status' => false, 'message' => 'Registration error: ' . $e->getMessage()];
        }
    }

    public static function logout(String $logout_id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['unique_id'])) {
            header("Location: ../index.php");
            exit;
        }

        self::updateStatus($logout_id, "Offline"); //* Update status

        session_unset();

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 3600,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy(); //* # Destroy session

        header("location: ../index.php"); //? Redirect to login page
        exit;
    }

    public static function search(string $searchTerm, string $outgoing_id): array
    {
        try {
            $conn = parent::getConnection();
            $stmt = $conn->prepare("
                SELECT * FROM users 
                WHERE unique_id != :out_id
                  AND fullname LIKE :search
            ");
            $stmt->execute([
                ':out_id' => $outgoing_id,
                ':search' => "%$searchTerm%"
            ]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {

            error_log("User search error: " . $e->getMessage());
            return [];
            
        }
    }

    public static function find(string $id): ?array
    {
        $conn = parent::getConnection();
        $stmt = $conn->prepare("SELECT unique_id, fullname, img, status FROM users WHERE unique_id = :id");
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    // HACK: update status
    public static function updateStatus($user_id, $status)
    {
        try {
            $conn = parent::getConnection();
            $stmt = $conn->prepare("UPDATE users SET status = :status WHERE unique_id = :id");
            return $stmt->execute([":status" => $status, ":id" => $user_id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function getUsers(String $outgoing_id)
    {
        try {
            $conn = parent::getConnection();
            $stmt = $conn->prepare("
            SELECT u.*, MAX(m.created_at) AS latest_msg 
            FROM users u
            LEFT JOIN (
                SELECT * FROM messages
                WHERE incoming_msg_id = :outgoing OR outgoing_msg_id = :outgoing
            ) m ON u.unique_id = IF(m.incoming_msg_id = :outgoing, m.outgoing_msg_id, m.incoming_msg_id)
            WHERE u.unique_id != :outgoing
            GROUP BY u.unique_id
            ORDER BY latest_msg DESC
        ");
            $stmt->execute([':outgoing' => $outgoing_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

}

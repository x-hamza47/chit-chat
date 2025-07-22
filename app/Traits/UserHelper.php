<?php

namespace App\Traits;

trait UserHelper {

    // Hack: Username Validation
    protected static function isValidUsername(String $uname): bool
    {
        return (bool) preg_match('/^(?=[a-zA-Z0-9]*[._@])[a-zA-Z0-9._@]{3,30}$/', $uname);
    }

    // Hack: Check username exist
    protected static function userExist(String $uname): bool
    {
        $conn = parent::getConnection();

        $stmt = $conn->prepare("SELECT 1 FROM users WHERE uname = :uname LIMIT 1");
        $stmt->execute([":uname" => $uname]);

        return $stmt->fetchColumn() !== false;
    }

    
}
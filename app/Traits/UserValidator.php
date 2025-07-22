<?php

namespace App\Traits;

trait UserValidator
{
    public static function validateRegister(string $fullname, string $uname, string $pass): array
    {
        if (empty($fullname) || empty($uname) || empty($pass)) {
            return ['status' => false, 'message' => 'All fields are required'];
        }

        if (!self::isValidUsername($uname)) {
            return ['status' => false, 'message' => "$uname - Invalid username format!"];
        }

        if (self::userExist($uname)) {
            return ['status' => false, 'message' => "$uname - Username already taken!"];
        }

        return ['status' => true];
    }

    public static function validateLogin(string $uname, string $password): array
    {
        if (empty($uname) || empty($password)) {
            return ['status' => false, 'message' => 'All fields are required'];
        }

        return ['status' => true];
    }
}

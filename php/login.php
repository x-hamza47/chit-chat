<?php

use App\User;

require_once __DIR__ . '/../vendor/autoload.php';

session_start(); #-- Session start

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uname = $_POST['uname'] ?? '';
    $pass = $_POST['pass'] ?? '';

    $response = User::login($uname, $pass);

    echo $response['message'];
    exit;
}

echo json_encode(['status' => false, 'message' => 'Invalid request']);
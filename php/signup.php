<?php

use App\User;

require_once __DIR__ . '/../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST'){

    $data = [
        'fullname' => $_POST['fullname'],
        'uname' => $_POST['uname'],
        'pass' => $_POST['pass'],
    ];

    $response = User::register($data);

    echo json_encode($response);
    exit;
}
echo json_encode(['status' => false, 'message' => 'Invalid request']);
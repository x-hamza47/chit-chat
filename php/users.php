<?php

use App\User;

require_once __DIR__ . '/../vendor/autoload.php';

session_start();

if (!isset($_SESSION['unique_id'])) {
    header("Location: ../index.php");
    exit;
}

$outgoing_id = $_SESSION['unique_id'];
$output = "";

$users = User::getUsers($outgoing_id);

echo User::usersList($users, $outgoing_id);

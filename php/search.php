<?php

use App\User;

require_once __DIR__ . '/../vendor/autoload.php';

session_start();

$search_term = trim($_POST['search'] ?? ''); 
$outgoing_id = $_SESSION['unique_id'] ?? null;

if (!$outgoing_id) {
    echo "Session expired.";
    exit;
}

$users = User::search($search_term, $outgoing_id);

if (count($users) > 0) 
    echo User::usersList($users, $outgoing_id);
else
    echo "No users were found related to your search term.";

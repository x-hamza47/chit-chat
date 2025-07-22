<?php

use App\User;

require_once __DIR__ . '/../vendor/autoload.php';

$logout_id = $_GET['logout_id'] ?? null;

User::logout($logout_id);

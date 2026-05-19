<?php
// post.php  — Entry point for "Post Detail"

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/controllers/PostController.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$ctrl = new PostController();
$ctrl->detail();

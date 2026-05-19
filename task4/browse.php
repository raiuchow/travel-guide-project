<?php
// browse.php  — Entry point for "Browse Published Posts"
// Bootstraps session, loads controller, dispatches.

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/controllers/PostController.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$ctrl = new PostController();
$ctrl->browse();

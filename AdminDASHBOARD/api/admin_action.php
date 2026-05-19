<?php
require_once __DIR__ . '/../controllers/AdminController.php';
$action = $_GET['action'] ?? '';
(new AdminController())->api($action);

<?php
/**
 * Logout - Simple Version
 */

session_start();
$_SESSION = [];
session_destroy();
header('Location: /login.php');
exit;

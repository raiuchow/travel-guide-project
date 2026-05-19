<?php
// views/partials/header.php
// $pageTitle, $user must be set before including this file.
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = $pageTitle ?? 'Travel Guide';
$user      = $user      ?? currentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle) ?> | Travel Guide</title>

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,700;1,500&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="/public/css/style.css">
</head>
<body>

<nav class="navbar">
  <a class="navbar-brand" href="/browse.php">
    <span class="brand-icon">✦</span> TravelGuide
  </a>
  <div class="navbar-links">
    <a href="/browse.php">Browse</a>
    <?php if ($user['role'] === 'user' && $user['is_verified']): ?>
      <a href="/wishlist.php">Wishlist</a>
    <?php endif; ?>
    <?php if ($user['role'] === 'scout'): ?>
      <a href="/scout/dashboard.php">My Requests</a>
    <?php endif; ?>
    <?php if ($user['role'] === 'admin'): ?>
      <a href="/admin/dashboard.php">Admin</a>
    <?php endif; ?>
    <?php if ($user['id']): ?>
      <a href="/profile.php"><?= htmlspecialchars($user['name']) ?></a>
      <a href="/logout.php" class="btn-logout">Logout</a>
    <?php else: ?>
      <a href="/login.php">Login</a>
      <a href="/register.php" class="btn-primary">Register</a>
    <?php endif; ?>
  </div>
</nav>

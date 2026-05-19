<?php session_start(); ?>

<nav class="navbar navbar-dark bg-dark shadow p-3">
<div class="container">

<a href="home.php" class="navbar-brand fw-bold">
Travel Guide
</a>

<div>

<?php if(isset($_SESSION['user_id'])): ?>

<a href="profile.php" class="btn btn-outline-light btn-sm me-2">
Profile
</a>

<?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'user'): ?>
<a href="wishlist.php" class="btn btn-warning btn-sm me-2">
Wishlist
</a>
<?php endif; ?>

<a href="logout.php" class="btn btn-danger btn-sm">
Logout
</a>

<?php else: ?>

<a href="login.php" class="btn btn-outline-light btn-sm me-2">
Login
</a>

<a href="register.php" class="btn btn-primary btn-sm">
Register
</a>

<?php endif; ?>

</div>

</div>
</nav>
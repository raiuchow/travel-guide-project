<?php
session_start();
include 'config/db.php';
?>
<!DOCTYPE html>
<html>
<head>
<title>Home</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background: #f4f6f9;
    font-family: Arial;
}

.nav-card{
    background: white;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.welcome-box{
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    padding: 30px;
    border-radius: 15px;
}

.btn-custom{
    border-radius: 10px;
    padding: 10px 15px;
    margin-right: 5px;
}
</style>
</head>

<body>

<div class="container mt-5">

<?php if(!isset($_SESSION['user_id'])): ?>

<div class="text-center welcome-box">

<h2>Welcome to Travel Guide 🌍</h2>
<p>Discover amazing places around the world</p>

<a href="login.php" class="btn btn-light btn-custom">Login</a>
<a href="register.php" class="btn btn-warning btn-custom">Register</a>

</div>

<?php else: ?>

<?php if($_SESSION['role'] == 'user' && isset($_SESSION['is_verified']) && $_SESSION['is_verified'] == 0): ?>

<div class="alert alert-warning text-center">
    <h4>Your account is pending admin approval ⏳</h4>
    <p>Please wait until admin verifies your account.</p>
</div>

<?php else: ?>

<div class="welcome-box mb-4">

<h2>Welcome, <?php echo $_SESSION['name']; ?> 👋</h2>

<p>
Role: <strong><?php echo $_SESSION['role']; ?></strong>
</p>

</div>

<div class="nav-card mb-4">

<a href="profile.php" class="btn btn-primary btn-custom">Profile</a>

<?php if($_SESSION['role'] == 'user'): ?>
<a href="wishlist.php" class="btn btn-warning btn-custom">Wishlist</a>
<?php endif; ?>

<a href="logout.php" class="btn btn-danger btn-custom">Logout</a>

</div>

<div class="nav-card">

<h4>Latest Approved Posts</h4>

<div class="card p-3 mb-2">
    <h5>Sample Post 1</h5>
    <button class="btn btn-outline-success btn-sm" onclick="addWishlist(1)">
        Add to Wishlist
    </button>
</div>

<div class="card p-3">
    <h5>Sample Post 2</h5>
    <button class="btn btn-outline-success btn-sm" onclick="addWishlist(2)">
        Add to Wishlist
    </button>
</div>

<a href="browse.php" class="btn btn-dark mt-3">
    Browse All
</a>

</div>

<?php endif; ?>

<?php endif; ?>

</div>

<script>
function addWishlist(postId){

fetch("api/wishlist_add.php", {
    method: "POST",
    headers: {
        "Content-Type": "application/json"
    },
    body: JSON.stringify({
        post_id: postId
    })
})
.then(res => res.json())
.then(data => {
    alert(data.message);
});
}
</script>

</body>
</html>
<?php
session_start();
include 'config/db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

// get wishlist items
$stmt = $conn->prepare("
SELECT w.id, p.title, p.country, p.cost_level
FROM wishlist w
JOIN posts p ON w.post_id = p.id
WHERE w.user_id=?
");

$stmt->execute([$_SESSION['user_id']]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>

<title>Wishlist</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background:#f4f6f9;
}

.card-box{
    background:white;
    padding:20px;
    border-radius:15px;
    box-shadow:0 5px 15px rgba(0,0,0,0.1);
    margin-bottom:15px;
}

.header{
    background:linear-gradient(135deg,#667eea,#764ba2);
    color:white;
    padding:20px;
    border-radius:15px;
    margin-bottom:20px;
}
</style>

</head>

<body>

<div class="container mt-5">

<div class="header">
    <h2>My Wishlist ❤️</h2>
    <p>Saved travel destinations</p>
</div>

<div id="wishlistBox">

<?php if(count($items) == 0): ?>

<div class="alert alert-info">
    No items in wishlist yet.
</div>

<?php endif; ?>

<?php foreach($items as $item): ?>

<div class="card-box" id="item_<?php echo $item['id']; ?>">

    <h4>
        <?php echo htmlspecialchars($item['title']); ?>
    </h4>

    <p class="text-muted">
        <?php echo htmlspecialchars($item['country']); ?> |
        <strong><?php echo htmlspecialchars($item['cost_level']); ?></strong>
    </p>

    <button class="btn btn-danger btn-sm"
        onclick="removeWishlist(<?php echo $item['id']; ?>)">
        Remove
    </button>

</div>

<?php endforeach; ?>

</div>

</div>

<script>
function removeWishlist(id){

if(!confirm("Remove from wishlist?")) return;

fetch("api/wishlist_remove.php", {
    method: "POST",
    headers: {
        "Content-Type": "application/json"
    },
    body: JSON.stringify({id:id})
})
.then(res => res.json())
.then(data => {

    if(data.success){
        document.getElementById("item_"+id).remove();
    } else {
        alert(data.message || "Failed to remove");
    }

});
}
</script>

</body>
</html>
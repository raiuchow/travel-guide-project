<?php
session_start();
include '../config/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role']!='user' || $_SESSION['is_verified']!=1){
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
SELECT w.id, p.title, p.country, p.cost_level
FROM wishlist w
JOIN posts p ON w.post_id = p.id
WHERE w.user_id = ?
");
$stmt->execute([$user_id]);
$items = $stmt->fetchAll();
?>

<h2>My Wishlist</h2>

<?php foreach($items as $item): ?>
<div>
    <h4><?= $item['title'] ?></h4>
    <p><?= $item['country'] ?> | <?= $item['cost_level'] ?></p>

    <button onclick="removeItem(<?= $item['id'] ?>)">
        Remove
    </button>
</div>
<hr>
<?php endforeach; ?>

<script>
function removeItem(id){
    fetch("../api/wishlist/remove.php", {
        method: "DELETE",
        headers: {"Content-Type":"application/json"},
        body: JSON.stringify({id:id})
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        location.reload();
    });
}
</script>
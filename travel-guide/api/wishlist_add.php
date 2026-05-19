<?php
session_start();
include '../../config/db.php';

header("Content-Type: application/json");

if(!isset($_SESSION['user_id'])){
    echo json_encode(["status"=>"error","message"=>"Login required"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$post_id = $data['post_id'];
$user_id = $_SESSION['user_id'];

// check duplicate
$stmt = $conn->prepare("SELECT id FROM wishlist WHERE user_id=? AND post_id=?");
$stmt->execute([$user_id, $post_id]);

if($stmt->rowCount() > 0){
    echo json_encode(["status"=>"error","message"=>"Already in wishlist"]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO wishlist (user_id, post_id) VALUES (?, ?)");
$stmt->execute([$user_id, $post_id]);

echo json_encode(["status"=>"success","message"=>"Added to wishlist"]);
?>
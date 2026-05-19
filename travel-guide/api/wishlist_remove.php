<?php
session_start();
include '../../config/db.php';

header("Content-Type: application/json");

if(!isset($_SESSION['user_id'])){
    echo json_encode(["status"=>"error","message"=>"Login required"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'];
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("DELETE FROM wishlist WHERE id=? AND user_id=?");
$stmt->execute([$id, $user_id]);

echo json_encode(["status"=>"success","message"=>"Removed"]);
?>
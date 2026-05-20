<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "travel_guide");
header("Content-Type: application/json");

if(!isset($_SESSION['user_id'])){
    echo json_encode(["status"=>"error","message"=>"Login required"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$post_id = $data['post_id'];
$user_id = $_SESSION['user_id'];

// check duplicate
$check = mysqli_prepare($conn, "SELECT id FROM wishlist WHERE user_id=? AND post_id=?");
mysqli_stmt_bind_param($check, "ii", $user_id, $post_id);
mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);

if(mysqli_stmt_num_rows($check) > 0){
    echo json_encode(["status"=>"error","message"=>"Already in wishlist"]);
    exit;
}

$stmt = mysqli_prepare($conn, "INSERT INTO wishlist (user_id, post_id) VALUES (?, ?)");
mysqli_stmt_bind_param($stmt, "ii", $user_id, $post_id);
mysqli_stmt_execute($stmt);

echo json_encode(["status"=>"success","message"=>"Added to wishlist"]);
?>

<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "travel_guide");
header("Content-Type: application/json");

if(!isset($_SESSION['user_id'])){
    echo json_encode(["status"=>"error","message"=>"Login required"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'];
$user_id = $_SESSION['user_id'];

$stmt = mysqli_prepare($conn, "DELETE FROM wishlist WHERE id=? AND user_id=?");
mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);
mysqli_stmt_execute($stmt);

echo json_encode(["status"=>"success","message"=>"Removed"]);
?>

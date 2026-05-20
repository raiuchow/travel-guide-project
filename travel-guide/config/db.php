<?php
$conn = mysqli_connect("localhost", "root", "", "travel_guide");

if(!$conn){
    die("Connection failed: " . mysqli_connect_error());
}
?>

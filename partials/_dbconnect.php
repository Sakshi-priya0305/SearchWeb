<?php
$server = "localhost";
$username = "root";
$password = "";


$conn_users = mysqli_connect($server, $username, $password, "users123");
if (!$conn_users) {
    die("Error: " . mysqli_connect_error());
}


$conn_search_app = mysqli_connect($server, $username, $password, "search_app");
if (!$conn_search_app) {
    die("Error: " . mysqli_connect_error());
}
?>


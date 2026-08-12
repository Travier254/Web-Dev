<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'travifit';

$conn = mysqli_connect($host, $user, $pass);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS $db");
mysqli_select_db($conn, $db);
?>
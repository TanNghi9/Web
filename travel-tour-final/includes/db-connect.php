<?php
// ============================================================
// includes/db-connect.php – Database Connection
// ============================================================
$host     = 'localhost';
$username = 'root';
$password = '';
$database = 'traveltour_db';

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');

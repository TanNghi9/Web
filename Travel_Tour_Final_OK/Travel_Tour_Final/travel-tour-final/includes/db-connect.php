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

// Helper: chuẩn hóa đường dẫn ảnh (xử lý cả /images/... lẫn images/... lẫn uploads/...)
function img_url($path) {
    if (empty($path)) return 'images/placeholder.jpg';
    // Bỏ dấu / ở đầu nếu có
    $path = ltrim($path, '/');
    return htmlspecialchars($path);
}

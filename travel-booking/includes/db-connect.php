<?php
// ============================================
// DATABASE CONNECTION
// ============================================
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'travel_booking');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$conn->set_charset('utf8mb4');

if ($conn->connect_error) {
    die('<div style="text-align:center;padding:50px;font-family:sans-serif;">
        <h2>Khong the ket noi database</h2>
        <p>Vui long kiem tra cau hinh MySQL.</p>
        <small>' . $conn->connect_error . '</small>
    </div>');
}

// Session start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// BASE_URL: tự động lấy từ vị trí file db-connect.php
// dirname(__FILE__) = .../travel-booking/includes
// dirname(dirname(__FILE__)) = .../travel-booking
if (!defined('BASE_URL')) {
    $projectAbsPath = str_replace('\\', '/', dirname(dirname(__FILE__)));
    $docRoot        = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/');
    $base           = str_replace($docRoot, '', $projectAbsPath);
    $base           = '/' . trim($base, '/');
    if ($base === '/') $base = '';
    define('BASE_URL', $base);
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}
function requireAdmin() {
    if (!isAdmin()) {
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }
}
function sanitize($data) {
    global $conn;
    return htmlspecialchars(strip_tags(trim($conn->real_escape_string($data))));
}
function formatPrice($price) {
    return number_format($price, 0, ',', '.') . ' VND';
}
function getStars($rating) {
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        $stars .= $i <= $rating ? '★' : '☆';
    }
    return $stars;
}
?>

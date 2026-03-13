<?php
require_once 'includes/db-connect.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tours.php');
    exit;
}

$tourId        = intval($_POST['tour_id']        ?? 0);
$numPeople     = intval($_POST['num_people']      ?? 1);
$contactName   = trim($_POST['contact_name']      ?? '');
$contactPhone  = trim($_POST['contact_phone']     ?? '');
$specialReq    = trim($_POST['special_requests']  ?? '');
$userId        = $_SESSION['user_id'];

// Validate
$errors = [];
if (!$tourId)                       $errors[] = 'Tour không hợp lệ.';
if ($numPeople < 1)                 $errors[] = 'Số người phải ít nhất là 1.';
if (!$contactName)                  $errors[] = 'Vui lòng nhập tên liên hệ.';
if (!preg_match('/^0[0-9]{9}$/', $contactPhone)) $errors[] = 'Số điện thoại không hợp lệ.';

if ($errors) {
    $_SESSION['error'] = implode(' ', $errors);
    header("Location: tour-detail.php?id=$tourId");
    exit;
}

// Fetch tour
$stmt = $conn->prepare("SELECT * FROM tours WHERE id=? AND status='active'");
$stmt->bind_param('i', $tourId);
$stmt->execute();
$tour = $stmt->get_result()->fetch_assoc();

if (!$tour) {
    $_SESSION['error'] = 'Tour không tồn tại hoặc không còn hoạt động.';
    header("Location: tours.php");
    exit;
}

$left = $tour['max_capacity'] - $tour['current_bookings'];
if ($numPeople > $left) {
    $_SESSION['error'] = "Chỉ còn $left chỗ trống. Vui lòng giảm số người.";
    header("Location: tour-detail.php?id=$tourId");
    exit;
}

$totalPrice = $tour['price'] * $numPeople;

// Insert booking
$stmt2 = $conn->prepare("
    INSERT INTO bookings (user_id, tour_id, num_people, total_price, status, contact_name, contact_phone, special_requests)
    VALUES (?, ?, ?, ?, 'pending', ?, ?, ?)
");
$stmt2->bind_param('iiidsss', $userId, $tourId, $numPeople, $totalPrice, $contactName, $contactPhone, $specialReq);

if ($stmt2->execute()) {
    // Update tour current_bookings
    $conn->query("UPDATE tours SET current_bookings = current_bookings + $numPeople WHERE id = $tourId");

    $_SESSION['success'] = "🎉 Đặt tour thành công! Chúng tôi sẽ liên hệ xác nhận trong 24h.";
    header("Location: my-bookings.php");
} else {
    $_SESSION['error'] = 'Có lỗi xảy ra. Vui lòng thử lại.';
    header("Location: tour-detail.php?id=$tourId");
}
exit;

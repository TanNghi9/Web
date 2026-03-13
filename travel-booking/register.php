<?php
require_once 'includes/db-connect.php';

if (isLoggedIn()) { header('Location: index.php'); exit; }

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['full_name'] ?? '');
    $email    = trim($_POST['email']     ?? '');
    $phone    = trim($_POST['phone']     ?? '');
    $password = trim($_POST['password']  ?? '');
    $confirm  = trim($_POST['password_confirm'] ?? '');

    if (!$name || !$email || !$password || !$confirm) {
        $error = 'Vui lòng điền đầy đủ thông tin.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email không hợp lệ.';
    } elseif ($phone && !preg_match('/^0[0-9]{9}$/', $phone)) {
        $error = 'Số điện thoại phải gồm 10 chữ số và bắt đầu bằng 0.';
    } elseif (strlen($password) < 6) {
        $error = 'Mật khẩu phải có ít nhất 6 ký tự.';
    } elseif ($password !== $confirm) {
        $error = 'Mật khẩu xác nhận không khớp.';
    } else {
        // Check email exists
        $check = $conn->prepare("SELECT id FROM users WHERE email=?");
        $check->bind_param('s', $email);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $error = 'Email này đã được đăng ký. Vui lòng dùng email khác.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, phone) VALUES (?,?,?,?)");
            $stmt->bind_param('ssss', $name, $email, $hashed, $phone);
            if ($stmt->execute()) {
                $_SESSION['reg_success'] = '🎉 Đăng ký thành công! Vui lòng đăng nhập.';
                header('Location: login.php');
                exit;
            } else {
                $error = 'Có lỗi xảy ra. Vui lòng thử lại.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Đăng Ký – TravelVN</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>
<body>
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-logo">✈ <span>Travel</span>VN</div>
        <h2>Tạo Tài Khoản Mới</h2>
        <p class="auth-sub">Đăng ký để khám phá hàng trăm tour hấp dẫn</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" id="registerForm" onsubmit="return validateForm('registerForm')">
            <div class="form-group" style="margin-bottom:14px;">
                <label class="form-label">👤 Họ và Tên <span style="color:red">*</span></label>
                <input type="text" name="full_name" class="form-input" placeholder="Nguyễn Văn A"
                       value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" required>
                <div class="field-error"></div>
            </div>
            <div class="form-group" style="margin-bottom:14px;">
                <label class="form-label">📧 Email <span style="color:red">*</span></label>
                <input type="email" name="email" class="form-input" placeholder="example@gmail.com"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                <div class="field-error"></div>
            </div>
            <div class="form-group" style="margin-bottom:14px;">
                <label class="form-label">📞 Số Điện Thoại</label>
                <input type="tel" name="phone" class="form-input" placeholder="0901234567"
                       value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                <div class="field-error"></div>
            </div>
            <div class="form-group" style="margin-bottom:14px;">
                <label class="form-label">🔒 Mật Khẩu <span style="color:red">*</span></label>
                <input type="password" name="password" id="password" class="form-input"
                       placeholder="Tối thiểu 6 ký tự" required>
                <div class="field-error"></div>
            </div>
            <div class="form-group" style="margin-bottom:24px;">
                <label class="form-label">🔒 Xác Nhận Mật Khẩu <span style="color:red">*</span></label>
                <input type="password" name="password_confirm" id="password_confirm" class="form-input"
                       placeholder="Nhập lại mật khẩu" required>
                <div class="field-error"></div>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">Đăng Ký →</button>
        </form>

        <div class="auth-divider"><span>đã có tài khoản?</span></div>
        <div class="auth-link"><a href="login.php">Đăng nhập ngay</a></div>
    </div>
</div>
<script src="<?= BASE_URL ?>/js/main.js"></script>
</body>
</html>

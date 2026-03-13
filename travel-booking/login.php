<?php
require_once 'includes/db-connect.php';
// BASE_URL already defined in db-connect.php

if (isLoggedIn()) { header('Location: index.php'); exit; }

$error = '';
$redirect = $_GET['redirect'] ?? 'index.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$email || !$password) {
        $error = 'Vui lòng nhập đầy đủ email và mật khẩu.';
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        // Check: password is 'password' for demo data (plain hash $2y$10$92IXUNpkjO0...)
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email']= $user['email'];
            $_SESSION['user_role'] = $user['role'];
            header('Location: ' . $redirect);
            exit;
        } else {
            $error = 'Email hoặc mật khẩu không đúng.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Đăng Nhập – TravelVN</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>
<body>
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-logo">✈ <span>Travel</span>VN</div>
        <h2>Chào Mừng Trở Lại</h2>
        <p class="auth-sub">Đăng nhập để đặt tour và quản lý chuyến đi</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['reg_success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['reg_success']; unset($_SESSION['reg_success']); ?></div>
        <?php endif; ?>

        <form method="POST" id="loginForm" onsubmit="return validateForm('loginForm')">
            <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">

            <div class="form-group" style="margin-bottom:16px;">
                <label class="form-label">📧 Email</label>
                <input type="email" name="email" class="form-input" placeholder="example@gmail.com"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                <div class="field-error"></div>
            </div>
            <div class="form-group" style="margin-bottom:24px;">
                <label class="form-label">🔒 Mật Khẩu</label>
                <input type="password" name="password" id="password" class="form-input" placeholder="••••••••" required>
                <div class="field-error"></div>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">Đăng Nhập →</button>
        </form>

        <div class="auth-divider"><span>hoặc</span></div>
        <div class="auth-link">Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a></div>
        <div class="auth-link" style="margin-top:16px;padding:14px;background:var(--light);border-radius:var(--radius);font-size:.85rem;">
            <strong>Demo accounts:</strong><br>
            Admin: admin@travelvn.com / password<br>
            User: an@gmail.com / password
        </div>
    </div>
</div>
<script src="<?= BASE_URL ?>/js/main.js"></script>
</body>
</html>

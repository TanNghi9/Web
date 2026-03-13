<?php require_once __DIR__ . '/db-connect.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' – TravelVN' : 'TravelVN – Kham Pha Viet Nam' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <?= isset($extraCSS) ? $extraCSS : '' ?>
</head>
<body>

<nav class="navbar">
    <div class="nav-container">
        <a href="<?= BASE_URL ?>/index.php" class="logo">
            <span class="logo-icon">✈</span> TravelVN
        </a>
        <button class="nav-toggle" id="navToggle">☰</button>
        <ul class="nav-menu" id="navMenu">
            <li><a href="<?= BASE_URL ?>/index.php">Trang Chủ</a></li>
            <li><a href="<?= BASE_URL ?>/tours.php">Tất Cả Tour</a></li>
            <?php if (isLoggedIn()): ?>
                <li><a href="<?= BASE_URL ?>/my-bookings.php">Booking Của Tôi</a></li>
                <?php if (isAdmin()): ?>
                    <li><a href="<?= BASE_URL ?>/admin/index.php" class="admin-link">⚙ Admin</a></li>
                <?php endif; ?>
                <li class="user-menu">
                    <a href="#" class="user-avatar-link">
                        👤 <?= htmlspecialchars($_SESSION['user_name']) ?>
                    </a>
                    <div class="user-dropdown">
                        <a href="<?= BASE_URL ?>/my-bookings.php">📋 Booking Của Tôi</a>
                        <a href="<?= BASE_URL ?>/logout.php">🚪 Đăng Xuất</a>
                    </div>
                </li>
            <?php else: ?>
                <li><a href="<?= BASE_URL ?>/login.php" class="btn-nav-login">Đăng Nhập</a></li>
                <li><a href="<?= BASE_URL ?>/register.php" class="btn-nav-register">Đăng Ký</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

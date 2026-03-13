<?php
require_once __DIR__ . '/../includes/db-connect.php';
requireAdmin();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= isset($pageTitle) ? $pageTitle . ' – Admin' : 'TravelVN Admin' ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/admin.css">
</head>
<body class="admin-body">
<div class="admin-layout">

<aside class="admin-sidebar">
    <div class="admin-logo">✈ <span>Travel</span>VN <span style="font-size:.7rem;color:#64748B;display:block;font-family:'DM Sans'">Admin Panel</span></div>
    <nav class="admin-nav">
        <div class="nav-section">Tổng Quan</div>
        <a href="<?= BASE_URL ?>/admin/index.php"    class="<?= basename($_SERVER['PHP_SELF'])=='index.php'?'active':'' ?>">📊 Dashboard</a>
        <div class="nav-section">Quản Lý</div>
        <a href="<?= BASE_URL ?>/admin/tours.php"    class="<?= basename($_SERVER['PHP_SELF'])=='tours.php'?'active':'' ?>">🗺 Quản Lý Tour</a>
        <a href="<?= BASE_URL ?>/admin/bookings.php" class="<?= basename($_SERVER['PHP_SELF'])=='bookings.php'?'active':'' ?>">📋 Quản Lý Booking</a>
        <a href="<?= BASE_URL ?>/admin/users.php"    class="<?= basename($_SERVER['PHP_SELF'])=='users.php'?'active':'' ?>">👥 Quản Lý User</a>
    </nav>
    <div class="admin-footer-link">
        <a href="<?= BASE_URL ?>/index.php">← Về Trang Chủ</a><br><br>
        <a href="<?= BASE_URL ?>/logout.php" style="color:#E8533A;">🚪 Đăng Xuất</a>
    </div>
</aside>

<main class="admin-main">
    <div class="admin-topbar">
        <h1><?= isset($pageTitle) ? $pageTitle : 'Dashboard' ?></h1>
        <div style="font-size:.88rem;color:#94A3B8;">👤 <?= htmlspecialchars($_SESSION['user_name']) ?></div>
    </div>
    <div class="admin-content">
        <?php if (isset($_SESSION['admin_success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['admin_success']; unset($_SESSION['admin_success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['admin_error'])): ?>
            <div class="alert alert-error"><?= $_SESSION['admin_error']; unset($_SESSION['admin_error']); ?></div>
        <?php endif; ?>

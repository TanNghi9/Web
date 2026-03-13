<?php
$pageTitle = 'Dashboard';
require_once 'admin-header.php';

// Stats
$totalTours    = $conn->query("SELECT COUNT(*) FROM tours")->fetch_row()[0];
$totalBookings = $conn->query("SELECT COUNT(*) FROM bookings")->fetch_row()[0];
$totalUsers    = $conn->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetch_row()[0];
$totalRevenue  = $conn->query("SELECT COALESCE(SUM(total_price),0) FROM bookings WHERE status='confirmed'")->fetch_row()[0];
$pendingCount  = $conn->query("SELECT COUNT(*) FROM bookings WHERE status='pending'")->fetch_row()[0];

// Recent bookings
$recentBookings = $conn->query("
    SELECT b.*, u.full_name AS user_name, t.name AS tour_name
    FROM bookings b
    JOIN users u ON u.id = b.user_id
    JOIN tours t ON t.id = b.tour_id
    ORDER BY b.booking_date DESC LIMIT 8
");

// Revenue by tour (top 5)
$topTours = $conn->query("
    SELECT t.name, COUNT(b.id) AS bookings, COALESCE(SUM(b.total_price),0) AS revenue
    FROM tours t
    LEFT JOIN bookings b ON b.tour_id = t.id AND b.status='confirmed'
    GROUP BY t.id ORDER BY revenue DESC LIMIT 5
");

$statusColors = ['pending'=>'badge-gold','confirmed'=>'badge-green','cancelled'=>'badge-grey'];
$statusLabels = ['pending'=>'Chờ xác nhận','confirmed'=>'Đã xác nhận','cancelled'=>'Đã hủy'];
?>

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:#FEE9E5;">🗺</div>
        <div class="stat-info"><div class="num"><?= $totalTours ?></div><div class="lbl">Tổng Tour</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#E0F0F5;">📋</div>
        <div class="stat-info"><div class="num"><?= $totalBookings ?></div><div class="lbl">Tổng Booking</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#E0F5EC;">👥</div>
        <div class="stat-info"><div class="num"><?= $totalUsers ?></div><div class="lbl">Người Dùng</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#FEF5DF;">💰</div>
        <div class="stat-info"><div class="num" style="font-size:1.1rem;"><?= number_format($totalRevenue/1000000,1) ?>M</div><div class="lbl">Doanh Thu (VNĐ)</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#FFF3CD;">⏳</div>
        <div class="stat-info"><div class="num" style="color:#C9A84C;"><?= $pendingCount ?></div><div class="lbl">Chờ Xác Nhận</div></div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1.6fr 1fr;gap:20px;">

<!-- Recent Bookings -->
<div class="admin-card">
    <div class="admin-card-header">
        <h3>📋 Booking Gần Đây</h3>
        <a href="bookings.php" class="btn btn-outline btn-sm">Xem tất cả</a>
    </div>
    <div style="overflow-x:auto;">
    <table class="admin-table">
        <thead><tr>
            <th>Khách</th><th>Tour</th><th>Tiền</th><th>Trạng Thái</th><th>Thao Tác</th>
        </tr></thead>
        <tbody>
        <?php while ($bk = $recentBookings->fetch_assoc()):
            $sc = $statusColors[$bk['status']] ?? 'badge-grey';
            $sl = $statusLabels[$bk['status']] ?? $bk['status'];
        ?>
        <tr>
            <td>
                <div style="font-weight:600;font-size:.9rem;"><?= htmlspecialchars($bk['user_name']) ?></div>
                <div style="font-size:.78rem;color:#94A3B8;"><?= date('d/m H:i',strtotime($bk['booking_date'])) ?></div>
            </td>
            <td style="font-size:.88rem;max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($bk['tour_name']) ?></td>
            <td style="font-weight:600;color:#E8533A;font-size:.88rem;"><?= number_format($bk['total_price'],0,',','.') ?></td>
            <td><span class="badge <?= $sc ?>"><?= $sl ?></span></td>
            <td>
                <?php if ($bk['status'] === 'pending'): ?>
                <form method="POST" action="bookings.php" style="display:inline;">
                    <input type="hidden" name="confirm_id" value="<?= $bk['id'] ?>">
                    <button class="btn-icon btn-edit" title="Xác nhận">✔</button>
                </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    </div>
</div>

<!-- Top Tours -->
<div class="admin-card">
    <div class="admin-card-header"><h3>🏆 Tour Doanh Thu Cao</h3></div>
    <div style="padding:8px 0;">
    <?php $rank=1; while($t = $topTours->fetch_assoc()): ?>
    <div style="display:flex;align-items:center;gap:12px;padding:12px 20px;border-bottom:1px solid #F8FAFC;">
        <div style="width:28px;height:28px;background:<?= $rank==1?'#C9A84C':($rank==2?'#94A3B8':'#E2E8F0') ?>;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;color:<?= $rank<=2?'#fff':'#64748B' ?>;flex-shrink:0;"><?= $rank++ ?></div>
        <div style="flex:1;min-width:0;">
            <div style="font-size:.88rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($t['name']) ?></div>
            <div style="font-size:.78rem;color:#94A3B8;"><?= $t['bookings'] ?> bookings</div>
        </div>
        <div style="font-size:.88rem;font-weight:600;color:#1A5F7A;white-space:nowrap;"><?= number_format($t['revenue']/1000000,1) ?>M</div>
    </div>
    <?php endwhile; ?>
    </div>
</div>

</div>

<?php require_once 'admin-footer.php'; ?>

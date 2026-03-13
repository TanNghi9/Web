<?php
$pageTitle = 'Booking Của Tôi';
require_once 'includes/header.php';
requireLogin();

$userId = $_SESSION['user_id'];

// Handle cancel
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_id'])) {
    $bookingId = intval($_POST['cancel_id']);
    $stmt = $conn->prepare("SELECT b.*, t.current_bookings, b.num_people, t.id as tid FROM bookings b JOIN tours t ON t.id=b.tour_id WHERE b.id=? AND b.user_id=?");
    $stmt->bind_param('ii', $bookingId, $userId);
    $stmt->execute();
    $b = $stmt->get_result()->fetch_assoc();
    if ($b && $b['status'] === 'pending') {
        $conn->query("UPDATE bookings SET status='cancelled' WHERE id=$bookingId");
        $conn->query("UPDATE tours SET current_bookings = GREATEST(0, current_bookings - {$b['num_people']}) WHERE id={$b['tid']}");
        $_SESSION['success'] = 'Đã hủy booking thành công.';
    }
    header('Location: my-bookings.php');
    exit;
}

// Fetch bookings
$bookings = $conn->query("
    SELECT b.*, t.name AS tour_name, t.destination, t.image, t.duration_days, t.departure_date
    FROM bookings b
    JOIN tours t ON t.id = b.tour_id
    WHERE b.user_id = $userId
    ORDER BY b.booking_date DESC
");

$statusLabels = [
    'pending'   => ['Chờ xác nhận', 'badge-gold'],
    'confirmed' => ['Đã xác nhận',  'badge-green'],
    'cancelled' => ['Đã hủy',       'badge-grey'],
];
?>

<div class="page-hero">
    <h1>📋 Booking Của Tôi</h1>
    <p>Xin chào, <?= htmlspecialchars($_SESSION['user_name']) ?>! Quản lý các chuyến đi của bạn</p>
</div>

<div class="container bookings-layout">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <?php if ($bookings->num_rows > 0): ?>
    <div class="booking-table-wrap">
        <table class="booking-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tour</th>
                    <th>Ngày Đặt</th>
                    <th>Số Người</th>
                    <th>Tổng Tiền</th>
                    <th>Trạng Thái</th>
                    <th>Thao Tác</th>
                </tr>
            </thead>
            <tbody>
            <?php $i=1; while ($bk = $bookings->fetch_assoc()):
                [$label, $badgeClass] = $statusLabels[$bk['status']] ?? ['Unknown', 'badge-grey'];
                $img = $bk['image'] ? BASE_URL . '/uploads/'.$bk['image'] : 'https://picsum.photos/seed/'.$bk['tour_id'].'bk/200/200';
            ?>
                <tr>
                    <td style="color:var(--mid)">#<?= $i++ ?></td>
                    <td>
                        <div class="booking-tour-info">
                            <img src="<?= $img ?>" alt="" class="booking-tour-thumb">
                            <div>
                                <div style="font-weight:600;"><?= htmlspecialchars($bk['tour_name']) ?></div>
                                <div style="font-size:.82rem;color:var(--mid);">📍 <?= htmlspecialchars($bk['destination']) ?> · <?= $bk['duration_days'] ?> ngày</div>
                                <?php if($bk['departure_date']): ?>
                                    <div style="font-size:.82rem;color:var(--teal);">🚀 <?= date('d/m/Y', strtotime($bk['departure_date'])) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td><?= date('d/m/Y H:i', strtotime($bk['booking_date'])) ?></td>
                    <td style="text-align:center;"><?= $bk['num_people'] ?> người</td>
                    <td style="font-weight:600;color:var(--coral);"><?= formatPrice($bk['total_price']) ?></td>
                    <td><span class="badge <?= $badgeClass ?>"><?= $label ?></span></td>
                    <td>
                        <a href="tour-detail.php?id=<?= $bk['tour_id'] ?>" class="btn btn-outline btn-sm">Xem Tour</a>
                        <?php if ($bk['status'] === 'pending'): ?>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn hủy booking này?')">
                                <input type="hidden" name="cancel_id" value="<?= $bk['id'] ?>">
                                <button class="btn btn-sm" style="background:#dc3545;color:#fff;border:none;cursor:pointer;">Hủy</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="no-results">
        <div class="icon">🧳</div>
        <h3>Bạn chưa có booking nào</h3>
        <p>Hãy khám phá các tour và đặt chuyến đi đầu tiên của bạn!</p>
        <a href="tours.php" class="btn btn-primary" style="margin-top:16px;">🗺 Xem Tour Ngay</a>
    </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>

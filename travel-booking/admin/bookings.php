<?php
$pageTitle = 'Quản Lý Booking';
require_once 'admin-header.php';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm_id'])) {
        $bid = intval($_POST['confirm_id']);
        $conn->query("UPDATE bookings SET status='confirmed' WHERE id=$bid");
        $_SESSION['admin_success'] = 'Đã xác nhận booking #' . $bid;
    }
    if (isset($_POST['cancel_id'])) {
        $bid = intval($_POST['cancel_id']);
        // Restore seats
        $bk = $conn->query("SELECT tour_id, num_people FROM bookings WHERE id=$bid")->fetch_assoc();
        if ($bk) {
            $conn->query("UPDATE tours SET current_bookings = GREATEST(0, current_bookings - {$bk['num_people']}) WHERE id={$bk['tour_id']}");
        }
        $conn->query("UPDATE bookings SET status='cancelled' WHERE id=$bid");
        $_SESSION['admin_success'] = 'Đã hủy booking #' . $bid;
    }
    header('Location: bookings.php');
    exit;
}

// Filters
$perPage    = 12;
$page       = max(1, intval($_GET['page'] ?? 1));
$offset     = ($page - 1) * $perPage;
$statusF    = $_GET['status'] ?? '';
$searchF    = trim($_GET['search'] ?? '');

$where = [];
if ($statusF) $where[] = "b.status = '" . $conn->real_escape_string($statusF) . "'";
if ($searchF) {
    $like = '%' . $conn->real_escape_string($searchF) . '%';
    $where[] = "(u.full_name LIKE '$like' OR t.name LIKE '$like' OR b.contact_phone LIKE '$like')";
}
$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$total      = $conn->query("SELECT COUNT(*) FROM bookings b JOIN users u ON u.id=b.user_id JOIN tours t ON t.id=b.tour_id $whereSQL")->fetch_row()[0];
$totalPages = max(1, ceil($total / $perPage));
$bookings   = $conn->query("
    SELECT b.*, u.full_name AS user_name, u.email AS user_email, t.name AS tour_name, t.destination, t.image
    FROM bookings b
    JOIN users u ON u.id = b.user_id
    JOIN tours t ON t.id = b.tour_id
    $whereSQL
    ORDER BY b.booking_date DESC
    LIMIT $perPage OFFSET $offset
");

$statusColors = ['pending'=>'badge-gold','confirmed'=>'badge-green','cancelled'=>'badge-grey'];
$statusLabels = ['pending'=>'Chờ xác nhận','confirmed'=>'Đã xác nhận','cancelled'=>'Đã hủy'];
$tabs = [''=> 'Tất Cả', 'pending'=>'Chờ XN', 'confirmed'=>'Đã XN', 'cancelled'=>'Đã Hủy'];
?>

<!-- Filter bar -->
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <?php foreach($tabs as $val=>$label): ?>
            <a href="?<?= http_build_query(['status'=>$val,'search'=>$searchF,'page'=>1]) ?>"
               class="chip <?= $statusF===$val?'active':'' ?>"><?= $label ?></a>
        <?php endforeach; ?>
    </div>
    <form method="GET" style="display:flex;gap:8px;">
        <input type="hidden" name="status" value="<?= htmlspecialchars($statusF) ?>">
        <input type="text" name="search" value="<?= htmlspecialchars($searchF) ?>" placeholder="🔍 Tên, SĐT..." class="form-input" style="padding:9px 14px;width:220px;">
        <button class="btn btn-teal btn-sm">Tìm</button>
    </form>
</div>

<div class="admin-card">
    <div class="admin-card-header">
        <h3>📋 Danh Sách Booking <span style="color:#94A3B8;font-size:.85rem;">(<?= $total ?>)</span></h3>
    </div>
    <div style="overflow-x:auto;">
    <table class="admin-table">
        <thead><tr>
            <th>#</th><th>Khách Hàng</th><th>Tour</th><th>Số Người</th>
            <th>Tổng Tiền</th><th>Ngày Đặt</th><th>Trạng Thái</th><th>Thao Tác</th>
        </tr></thead>
        <tbody>
        <?php while ($bk = $bookings->fetch_assoc()):
            $sc = $statusColors[$bk['status']] ?? 'badge-grey';
            $sl = $statusLabels[$bk['status']] ?? $bk['status'];
            $img = $bk['image'] ? BASE_URL . '/uploads/'.$bk['image'] : 'https://picsum.photos/seed/'.$bk['tour_id'].'admbk/80/60';
        ?>
        <tr>
            <td style="color:#94A3B8;font-size:.85rem;">#<?= $bk['id'] ?></td>
            <td>
                <div style="font-weight:600;"><?= htmlspecialchars($bk['user_name']) ?></div>
                <div style="font-size:.78rem;color:#94A3B8;"><?= htmlspecialchars($bk['user_email']) ?></div>
                <div style="font-size:.78rem;color:#94A3B8;">📞 <?= htmlspecialchars($bk['contact_phone']) ?></div>
            </td>
            <td>
                <div style="display:flex;align-items:center;gap:10px;">
                    <img src="<?= $img ?>" style="width:48px;height:36px;object-fit:cover;border-radius:6px;" alt="">
                    <div>
                        <div style="font-size:.88rem;font-weight:600;max-width:160px;"><?= htmlspecialchars($bk['tour_name']) ?></div>
                        <div style="font-size:.76rem;color:#94A3B8;">📍 <?= htmlspecialchars($bk['destination']) ?></div>
                    </div>
                </div>
            </td>
            <td style="text-align:center;font-weight:600;"><?= $bk['num_people'] ?></td>
            <td style="font-weight:700;color:#E8533A;"><?= number_format($bk['total_price'],0,',','.') ?>đ</td>
            <td style="font-size:.85rem;color:#94A3B8;"><?= date('d/m/Y H:i', strtotime($bk['booking_date'])) ?></td>
            <td><span class="badge <?= $sc ?>"><?= $sl ?></span></td>
            <td>
                <div class="action-btns">
                <?php if ($bk['status'] === 'pending'): ?>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Xác nhận booking này?')">
                        <input type="hidden" name="confirm_id" value="<?= $bk['id'] ?>">
                        <button class="btn-icon btn-edit" title="Xác nhận">✔</button>
                    </form>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Hủy booking này?')">
                        <input type="hidden" name="cancel_id" value="<?= $bk['id'] ?>">
                        <button class="btn-icon btn-delete" title="Hủy">✖</button>
                    </form>
                <?php else: ?>
                    <span style="font-size:.8rem;color:#94A3B8;">–</span>
                <?php endif; ?>
                </div>
                <?php if ($bk['special_requests']): ?>
                    <div style="font-size:.75rem;color:#94A3B8;margin-top:4px;max-width:120px;" title="<?= htmlspecialchars($bk['special_requests']) ?>">
                        💬 <?= mb_substr(htmlspecialchars($bk['special_requests']),0,30) ?>...
                    </div>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    </div>

    <?php if ($totalPages > 1): ?>
    <div style="padding:16px 20px;display:flex;gap:6px;justify-content:center;">
        <?php for($i=1;$i<=$totalPages;$i++): ?>
            <a href="?<?= http_build_query(['status'=>$statusF,'search'=>$searchF,'page'=>$i]) ?>" class="page-btn <?= $i==$page?'active':'' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once 'admin-footer.php'; ?>

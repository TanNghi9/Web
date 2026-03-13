<?php
$pageTitle = 'Quản Lý Người Dùng';
require_once 'admin-header.php';

// Delete user
if ($_GET['action'] ?? '' === 'delete') {
    $uid = intval($_GET['id'] ?? 0);
    if ($uid && $uid !== (int)$_SESSION['user_id']) {
        $conn->query("DELETE FROM users WHERE id=$uid AND role='user'");
        $_SESSION['admin_success'] = 'Đã xóa người dùng.';
    }
    header('Location: users.php');
    exit;
}

// Filters
$perPage = 12;
$page    = max(1, intval($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;
$search  = trim($_GET['search'] ?? '');
$where   = $search ? "WHERE u.full_name LIKE '%" . $conn->real_escape_string($search) . "%' OR u.email LIKE '%" . $conn->real_escape_string($search) . "%'" : '';

$total      = $conn->query("SELECT COUNT(*) FROM users u $where")->fetch_row()[0];
$totalPages = max(1, ceil($total / $perPage));

$users = $conn->query("
    SELECT u.*,
           COUNT(DISTINCT b.id) AS booking_count,
           COALESCE(SUM(CASE WHEN b.status='confirmed' THEN b.total_price ELSE 0 END),0) AS total_spent
    FROM users u
    LEFT JOIN bookings b ON b.user_id = u.id
    $where
    GROUP BY u.id
    ORDER BY u.created_at DESC
    LIMIT $perPage OFFSET $offset
");
?>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
    <form method="GET" style="display:flex;gap:8px;">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="🔍 Tìm tên, email..." class="form-input" style="padding:9px 14px;width:260px;">
        <button class="btn btn-teal btn-sm">Tìm</button>
        <?php if($search): ?><a href="users.php" class="btn btn-outline btn-sm">Xóa lọc</a><?php endif; ?>
    </form>
    <div style="font-size:.88rem;color:#94A3B8;">Tổng: <?= $total ?> người dùng</div>
</div>

<div class="admin-card">
    <div style="overflow-x:auto;">
    <table class="admin-table">
        <thead><tr>
            <th>#</th><th>Người Dùng</th><th>Email</th><th>Số ĐT</th>
            <th>Booking</th><th>Chi Tiêu</th><th>Vai Trò</th><th>Ngày ĐK</th><th>Thao Tác</th>
        </tr></thead>
        <tbody>
        <?php while ($u = $users->fetch_assoc()): ?>
        <tr>
            <td style="color:#94A3B8;">#<?= $u['id'] ?></td>
            <td>
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#E8533A,#1A5F7A);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:.88rem;flex-shrink:0;">
                        <?= mb_strtoupper(mb_substr($u['full_name'],0,1)) ?>
                    </div>
                    <span style="font-weight:600;"><?= htmlspecialchars($u['full_name']) ?></span>
                </div>
            </td>
            <td style="font-size:.88rem;"><?= htmlspecialchars($u['email']) ?></td>
            <td style="font-size:.88rem;"><?= htmlspecialchars($u['phone'] ?? '–') ?></td>
            <td style="text-align:center;font-weight:600;"><?= $u['booking_count'] ?></td>
            <td style="font-weight:600;color:#E8533A;font-size:.88rem;">
                <?= $u['total_spent'] > 0 ? number_format($u['total_spent']/1000000,1).'M' : '–' ?>
            </td>
            <td>
                <span class="badge <?= $u['role']==='admin'?'badge-coral':'badge-teal' ?>">
                    <?= $u['role']==='admin' ? '⚙ Admin' : '👤 User' ?>
                </span>
            </td>
            <td style="font-size:.82rem;color:#94A3B8;"><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
            <td>
                <?php if ($u['role'] !== 'admin'): ?>
                <a href="users.php?action=delete&id=<?= $u['id'] ?>" class="btn-icon btn-delete"
                   onclick="return confirm('Xóa người dùng <?= htmlspecialchars($u['full_name']) ?>?')" title="Xóa">🗑</a>
                <?php else: ?>
                    <span style="color:#94A3B8;font-size:.8rem;">–</span>
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
            <a href="?<?= http_build_query(['search'=>$search,'page'=>$i]) ?>" class="page-btn <?= $i==$page?'active':'' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once 'admin-footer.php'; ?>

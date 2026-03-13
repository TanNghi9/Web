<?php
$pageTitle = 'Quản Lý Tour';
require_once 'admin-header.php';

$action = $_GET['action'] ?? 'list';
$editId = intval($_GET['id'] ?? 0);

// ── DELETE ──
if ($action === 'delete' && $editId) {
    $tour = $conn->query("SELECT image FROM tours WHERE id=$editId")->fetch_assoc();
    if ($tour && $tour['image']) {
        $imgPath = __DIR__ . '/../uploads/' . $tour['image'];
        if (file_exists($imgPath)) unlink($imgPath);
    }
    $conn->query("DELETE FROM tours WHERE id=$editId");
    $_SESSION['admin_success'] = 'Đã xóa tour thành công.';
    header('Location: tours.php');
    exit;
}

// ── SAVE (Add / Edit) ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name         = trim($_POST['name']          ?? '');
    $catId        = intval($_POST['category_id'] ?? 0);
    $destination  = trim($_POST['destination']   ?? '');
    $description  = trim($_POST['description']   ?? '');
    $highlights   = trim($_POST['highlights']    ?? '');
    $itinerary    = trim($_POST['itinerary']     ?? '');
    $durationDays = intval($_POST['duration_days'] ?? 1);
    $price        = intval($_POST['price']        ?? 0);
    $maxCap       = intval($_POST['max_capacity'] ?? 20);
    $departure    = trim($_POST['departure_date'] ?? '');
    $status       = $_POST['status'] ?? 'active';
    $postId       = intval($_POST['id'] ?? 0);

    // Slug generation
    $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
    $slug = trim($slug, '-') . '-' . time();

    // Image upload
    $imageName = $_POST['existing_image'] ?? '';
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];
        if (in_array($ext, $allowed) && $_FILES['image']['size'] < 5*1024*1024) {
            $newName = uniqid('tour_') . '.' . $ext;
            $uploadDir = __DIR__ . '/../uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $newName)) {
                // Delete old image
                if ($imageName && file_exists($uploadDir . $imageName)) unlink($uploadDir . $imageName);
                $imageName = $newName;
            }
        } else {
            $_SESSION['admin_error'] = 'File ảnh không hợp lệ (chỉ JPG/PNG/WEBP, tối đa 5MB).';
            header('Location: tours.php?action=' . ($postId ? "edit&id=$postId" : 'add'));
            exit;
        }
    }

    if ($postId) {
        // UPDATE
        $stmt = $conn->prepare("UPDATE tours SET category_id=?,name=?,destination=?,description=?,highlights=?,itinerary=?,duration_days=?,price=?,max_capacity=?,departure_date=?,status=?,image=? WHERE id=?");
        $depVal = $departure ?: null;
        $stmt->bind_param('issssssiisssi', $catId,$name,$destination,$description,$highlights,$itinerary,$durationDays,$price,$maxCap,$depVal,$status,$imageName,$postId);
        $stmt->execute();
        $_SESSION['admin_success'] = 'Cập nhật tour thành công!';
    } else {
        // INSERT
        $stmt = $conn->prepare("INSERT INTO tours (category_id,name,slug,destination,description,highlights,itinerary,duration_days,price,max_capacity,departure_date,status,image) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $depVal = $departure ?: null;
        $stmt->bind_param('issssssiissss', $catId,$name,$slug,$destination,$description,$highlights,$itinerary,$durationDays,$price,$maxCap,$depVal,$status,$imageName);
        $stmt->execute();
        $_SESSION['admin_success'] = 'Thêm tour mới thành công!';
    }
    header('Location: tours.php');
    exit;
}

// ── EDIT: fetch existing data ──
$editTour = null;
if ($action === 'edit' && $editId) {
    $editTour = $conn->query("SELECT * FROM tours WHERE id=$editId")->fetch_assoc();
    if (!$editTour) { header('Location: tours.php'); exit; }
}

// ── LIST ──
$perPage = 10;
$page    = max(1, intval($_GET['page'] ?? 1));
$search  = trim($_GET['search'] ?? '');
$where   = $search ? "WHERE t.name LIKE '%" . $conn->real_escape_string($search) . "%' OR t.destination LIKE '%" . $conn->real_escape_string($search) . "%'" : '';
$total   = $conn->query("SELECT COUNT(*) FROM tours t $where")->fetch_row()[0];
$totalPages = max(1, ceil($total / $perPage));
$offset  = ($page - 1) * $perPage;
$tours   = $conn->query("SELECT t.*, c.name AS cat_name FROM tours t JOIN categories c ON c.id=t.category_id $where ORDER BY t.id DESC LIMIT $perPage OFFSET $offset");
$categories = $conn->query("SELECT * FROM categories ORDER BY id");

$statusBadge = ['active'=>'badge-green','inactive'=>'badge-grey','full'=>'badge-coral'];
$statusLabel = ['active'=>'Hoạt động','inactive'=>'Tạm dừng','full'=>'Đã đầy'];
?>

<?php if ($action === 'list'): ?>
<!-- LIST VIEW -->
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
    <form method="GET" style="display:flex;gap:10px;">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-input" placeholder="🔍 Tìm tên tour, điểm đến..." style="width:260px;padding:9px 14px;">
        <button class="btn btn-teal btn-sm">Tìm</button>
        <?php if($search): ?><a href="tours.php" class="btn btn-outline btn-sm">Xóa lọc</a><?php endif; ?>
    </form>
    <a href="tours.php?action=add" class="btn btn-primary btn-sm">＋ Thêm Tour Mới</a>
</div>

<div class="admin-card">
    <div style="overflow-x:auto;">
    <table class="admin-table">
        <thead><tr>
            <th>ID</th><th>Ảnh</th><th>Tên Tour</th><th>Danh Mục</th><th>Điểm Đến</th>
            <th>Giá</th><th>Còn Chỗ</th><th>Trạng Thái</th><th>Thao Tác</th>
        </tr></thead>
        <tbody>
        <?php while ($t = $tours->fetch_assoc()):
            $left = $t['max_capacity'] - $t['current_bookings'];
            $img  = $t['image'] ? BASE_URL . '/uploads/'.$t['image'] : 'https://picsum.photos/seed/'.$t['id'].'adm/80/60';
        ?>
        <tr>
            <td style="color:#94A3B8">#<?= $t['id'] ?></td>
            <td><img src="<?= $img ?>" style="width:64px;height:48px;object-fit:cover;border-radius:6px;" alt=""></td>
            <td>
                <div style="font-weight:600;max-width:200px;"><?= htmlspecialchars($t['name']) ?></div>
                <div style="font-size:.78rem;color:#94A3B8;">📅 <?= $t['duration_days'] ?> ngày</div>
            </td>
            <td><?= htmlspecialchars($t['cat_name']) ?></td>
            <td>📍 <?= htmlspecialchars($t['destination']) ?></td>
            <td style="font-weight:600;color:#E8533A;"><?= number_format($t['price'],0,',','.') ?>đ</td>
            <td>
                <div style="font-size:.88rem;"><?= max(0,$left) ?>/<?= $t['max_capacity'] ?></div>
                <div style="height:4px;background:#eee;border-radius:2px;width:60px;margin-top:4px;">
                    <div style="height:100%;background:#E8533A;border-radius:2px;width:<?= $t['max_capacity']>0?round($t['current_bookings']/$t['max_capacity']*100):0 ?>%"></div>
                </div>
            </td>
            <td><span class="badge <?= $statusBadge[$t['status']] ?? 'badge-grey' ?>"><?= $statusLabel[$t['status']] ?? $t['status'] ?></span></td>
            <td>
                <div class="action-btns">
                    <a href="tours.php?action=edit&id=<?= $t['id'] ?>" class="btn-icon btn-edit" title="Chỉnh sửa">✏</a>
                    <a href="tours.php?action=delete&id=<?= $t['id'] ?>" class="btn-icon btn-delete" title="Xóa"
                       onclick="return confirm('Bạn có chắc muốn xóa tour này?')">🗑</a>
                </div>
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

<?php else: ?>
<!-- ADD / EDIT FORM -->
<div style="margin-bottom:16px;">
    <a href="tours.php" style="color:var(--teal);">← Quay lại danh sách</a>
</div>

<div class="admin-form-card">
    <h3 style="margin-bottom:24px;"><?= $action==='edit' ? '✏ Chỉnh Sửa Tour' : '＋ Thêm Tour Mới' ?></h3>
    <form method="POST" enctype="multipart/form-data">
        <?php if ($editTour): ?>
            <input type="hidden" name="id" value="<?= $editTour['id'] ?>">
            <input type="hidden" name="existing_image" value="<?= htmlspecialchars($editTour['image'] ?? '') ?>">
        <?php endif; ?>

        <div class="form-grid">
            <div class="form-group-admin form-full">
                <label>Tên Tour *</label>
                <input type="text" name="name" required placeholder="VD: Phú Quốc 4N3Đ – Đảo Ngọc"
                       value="<?= htmlspecialchars($editTour['name'] ?? '') ?>">
            </div>
            <div class="form-group-admin">
                <label>Danh Mục *</label>
                <select name="category_id" required>
                    <?php $categories->data_seek(0); while($c=$categories->fetch_assoc()): ?>
                        <option value="<?= $c['id'] ?>" <?= ($editTour['category_id']??0)==$c['id']?'selected':'' ?>>
                            <?= $c['icon'] ?> <?= htmlspecialchars($c['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group-admin">
                <label>Điểm Đến *</label>
                <input type="text" name="destination" required placeholder="VD: Phú Quốc, Kiên Giang"
                       value="<?= htmlspecialchars($editTour['destination'] ?? '') ?>">
            </div>
            <div class="form-group-admin">
                <label>Số Ngày *</label>
                <input type="number" name="duration_days" min="1" required value="<?= $editTour['duration_days'] ?? 3 ?>">
            </div>
            <div class="form-group-admin">
                <label>Giá / Người (VNĐ) *</label>
                <input type="number" name="price" min="0" required value="<?= $editTour['price'] ?? '' ?>" placeholder="VD: 2990000">
            </div>
            <div class="form-group-admin">
                <label>Sức Chứa Tối Đa</label>
                <input type="number" name="max_capacity" min="1" value="<?= $editTour['max_capacity'] ?? 20 ?>">
            </div>
            <div class="form-group-admin">
                <label>Ngày Khởi Hành</label>
                <input type="date" name="departure_date" value="<?= $editTour['departure_date'] ?? '' ?>">
            </div>
            <div class="form-group-admin">
                <label>Trạng Thái</label>
                <select name="status">
                    <option value="active"   <?= ($editTour['status']??'active')==='active'   ?'selected':'' ?>>Hoạt động</option>
                    <option value="inactive" <?= ($editTour['status']??'')==='inactive' ?'selected':'' ?>>Tạm dừng</option>
                    <option value="full"     <?= ($editTour['status']??'')==='full'     ?'selected':'' ?>>Đã đầy</option>
                </select>
            </div>
            <div class="form-group-admin form-full">
                <label>Mô Tả</label>
                <textarea name="description" rows="4" placeholder="Mô tả chi tiết về tour..."><?= htmlspecialchars($editTour['description'] ?? '') ?></textarea>
            </div>
            <div class="form-group-admin form-full">
                <label>Điểm Nổi Bật (phân cách bằng |)</label>
                <input type="text" name="highlights" placeholder="Bãi biển đẹp|Hướng dẫn viên|Ăn uống bao gồm"
                       value="<?= htmlspecialchars($editTour['highlights'] ?? '') ?>">
            </div>
            <div class="form-group-admin form-full">
                <label>Lịch Trình (mỗi ngày 1 dòng)</label>
                <textarea name="itinerary" rows="5" placeholder="Ngày 1: Đến nơi, nhận phòng&#10;Ngày 2: Tham quan..."><?= htmlspecialchars($editTour['itinerary'] ?? '') ?></textarea>
            </div>
            <div class="form-group-admin form-full">
                <label>Ảnh Tour (JPG/PNG/WEBP, tối đa 5MB)</label>
                <?php if (!empty($editTour['image'])): ?>
                    <img src=" . BASE_URL . "/uploads/<?= htmlspecialchars($editTour['image']) ?>" class="current-img" alt="Ảnh hiện tại"><br>
                    <small style="color:#94A3B8;">Tải ảnh mới để thay thế</small><br>
                <?php endif; ?>
                <input type="file" name="image" accept="image/jpeg,image/png,image/webp" style="margin-top:8px;">
            </div>
        </div>

        <div style="margin-top:24px;display:flex;gap:12px;">
            <button type="submit" class="btn btn-primary"><?= $action==='edit' ? '💾 Lưu Thay Đổi' : '➕ Thêm Tour' ?></button>
            <a href="tours.php" class="btn btn-outline">Hủy</a>
        </div>
    </form>
</div>
<?php endif; ?>

<?php require_once 'admin-footer.php'; ?>

<?php
// ============================================================
// admin-tours.php – Admin Panel (CRUD + Image Upload)
// ============================================================
include 'includes/db-connect.php';

$action  = isset($_GET['action']) ? trim($_GET['action']) : 'list';
$tour_id = isset($_GET['id'])     ? (int)$_GET['id']      : 0;
$msg     = '';
$error   = '';

// ── DELETE ──
if ($action === 'delete' && $tour_id > 0) {
    $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT image_url FROM tours WHERE id = $tour_id"));
    if ($row) {
        if (!empty($row['image_url']) && strpos($row['image_url'], 'uploads/') === 0 && file_exists($row['image_url'])) {
            unlink($row['image_url']);
        }
        mysqli_query($conn, "DELETE FROM tours WHERE id = $tour_id");
        header("Location: admin-tours.php?msg=deleted");
        exit;
    }
}

if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'deleted') $msg = "✓ Tour deleted successfully.";
    if ($_GET['msg'] === 'added')   $msg = "✓ New tour added successfully.";
    if ($_GET['msg'] === 'updated') $msg = "✓ Tour updated successfully.";
}

// ── Image Upload Helper ──
function handleImageUpload($file_key)
{
    if (!isset($_FILES[$file_key]) || $_FILES[$file_key]['error'] !== 0) return null;
    $file    = $_FILES[$file_key];
    $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($ext, $allowed))  return ['error' => 'Invalid file type. Allowed: JPG, PNG, GIF, WEBP.'];
    if ($file['size'] > 5242880)    return ['error' => 'File too large. Maximum size is 5MB.'];
    $dir = 'uploads/';
    if (!file_exists($dir)) mkdir($dir, 0755, true);
    $filename = 'tour_' . uniqid() . '.' . $ext;
    $path = $dir . $filename;
    if (move_uploaded_file($file['tmp_name'], $path)) return ['url' => $path];
    return ['error' => 'Upload failed. Please check folder permissions.'];
}

// ── ADD ──
if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim(mysqli_real_escape_string($conn, $_POST['title']));
    $slug        = trim(mysqli_real_escape_string($conn, $_POST['slug']));
    $description = trim(mysqli_real_escape_string($conn, $_POST['description']));
    $content     = trim(mysqli_real_escape_string($conn, $_POST['content']));
    $price       = (int)$_POST['price'];
    $duration    = (int)$_POST['duration'];
    $max_people  = (int)$_POST['max_people'];
    $destination = trim(mysqli_real_escape_string($conn, $_POST['destination']));
    $category_id = (int)$_POST['category_id'];
    $status      = in_array($_POST['status'], ['active', 'inactive']) ? $_POST['status'] : 'active';

    if (empty($title) || empty($description) || empty($destination) || $category_id == 0) {
        $error = "Please fill in all required fields.";
    } else {
        $img = handleImageUpload('image');
        if (!$img) {
            $error = "Please select an image for the tour.";
        } elseif (isset($img['error'])) {
            $error = $img['error'];
        } else {
            $image_url = mysqli_real_escape_string($conn, $img['url']);
            $sql = "INSERT INTO tours (title,slug,description,content,price,duration,max_people,destination,category_id,image_url,status)
                    VALUES ('$title','$slug','$description','$content',$price,$duration,$max_people,'$destination',$category_id,'$image_url','$status')";
            if (mysqli_query($conn, $sql)) {
                header("Location: admin-tours.php?msg=added");
                exit;
            } else {
                $error = "DB Error: " . mysqli_error($conn);
                if (file_exists($img['url'])) unlink($img['url']);
            }
        }
    }
}

// ── UPDATE ──
if ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim(mysqli_real_escape_string($conn, $_POST['title']));
    $slug        = trim(mysqli_real_escape_string($conn, $_POST['slug']));
    $description = trim(mysqli_real_escape_string($conn, $_POST['description']));
    $content     = trim(mysqli_real_escape_string($conn, $_POST['content']));
    $price       = (int)$_POST['price'];
    $duration    = (int)$_POST['duration'];
    $max_people  = (int)$_POST['max_people'];
    $destination = trim(mysqli_real_escape_string($conn, $_POST['destination']));
    $category_id = (int)$_POST['category_id'];
    $status      = in_array($_POST['status'], ['active', 'inactive']) ? $_POST['status'] : 'active';
    $old_image   = mysqli_real_escape_string($conn, $_POST['old_image']);

    if (empty($title) || empty($description) || empty($destination) || $category_id == 0) {
        $error = "Please fill in all required fields.";
    } else {
        $image_url = $old_image;
        $img = handleImageUpload('image');
        if ($img && !isset($img['error'])) {
            if (!empty($old_image) && strpos($old_image, 'uploads/') === 0 && file_exists($old_image)) unlink($old_image);
            $image_url = mysqli_real_escape_string($conn, $img['url']);
        } elseif ($img && isset($img['error'])) {
            $error = $img['error'];
        }
        if (empty($error)) {
            $sql = "UPDATE tours SET title='$title',slug='$slug',description='$description',content='$content',
                    price=$price,duration=$duration,max_people=$max_people,destination='$destination',
                    category_id=$category_id,image_url='$image_url',status='$status' WHERE id=$tour_id";
            if (mysqli_query($conn, $sql)) {
                header("Location: admin-tours.php?msg=updated");
                exit;
            } else {
                $error = "DB Error: " . mysqli_error($conn);
            }
        }
    }
}

$edit_tour = null;
if ($action === 'edit' && $tour_id > 0) {
    $r = mysqli_query($conn, "SELECT * FROM tours WHERE id = $tour_id");
    if (mysqli_num_rows($r) == 0) {
        header("Location: admin-tours.php");
        exit;
    }
    $edit_tour = mysqli_fetch_assoc($r);
}

$per_page     = 8;
$current_page = max(1, isset($_GET['page']) ? (int)$_GET['page'] : 1);
$offset       = ($current_page - 1) * $per_page;
$s            = isset($_GET['s']) ? trim(mysqli_real_escape_string($conn, $_GET['s'])) : '';
$list_where   = $s ? "WHERE t.title LIKE '%$s%' OR t.destination LIKE '%$s%'" : "";
$total_list   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as n FROM tours t $list_where"))['n'];
$total_pages  = max(1, ceil($total_list / $per_page));
$tours_list   = mysqli_query($conn, "SELECT t.*, c.name as cat_name FROM tours t JOIN categories c ON t.category_id = c.id $list_where ORDER BY t.created_at DESC LIMIT $per_page OFFSET $offset");
$categories   = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");

$stats = [
    'tours'    => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as n FROM tours"))['n'],
    'active'   => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as n FROM tours WHERE status='active'"))['n'],
    'bookings' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as n FROM bookings"))['n'],
    'pending'  => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as n FROM bookings WHERE status='pending'"))['n'],
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel – VietNam Travel</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background: #f0f2f5;
            margin: 0;
            padding: 0;
        }

        .admin-layout {
            display: grid;
            grid-template-columns: 240px 1fr;
            min-height: 100vh;
        }

        /* ── FIXED LAYOUT ISSUE HERE ── */
        .admin-sidebar {
            background: var(--dark);
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 100;
        }

        .admin-main {
            padding: 36px 40px;
            width: 100%;
            box-sizing: border-box;
        }

        .admin-logo-wrap {
            padding: 28px 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .admin-logo-wrap a {
            font-family: var(--font-display);
            font-size: 1.2rem;
            color: var(--white);
            text-decoration: none;
        }

        .admin-logo-wrap a span {
            color: var(--gold);
        }

        .admin-logo-wrap small {
            display: block;
            font-size: 0.7rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.35);
            margin-top: 4px;
        }

        .admin-nav {
            padding: 16px 0;
        }

        .admin-nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 24px;
            color: rgba(255, 255, 255, 0.55);
            font-size: 0.85rem;
            font-weight: 600;
            transition: var(--transition);
            letter-spacing: 0.5px;
            text-decoration: none;
        }

        .admin-nav a:hover,
        .admin-nav a.active {
            color: var(--white);
            background: rgba(201, 168, 76, 0.1);
            border-left: 3px solid var(--gold);
            padding-left: 21px;
        }

        .admin-topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .admin-topbar h1 {
            font-size: 1.6rem;
            color: var(--dark);
            margin: 0;
        }

        .admin-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            padding: 24px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        }

        .stat-card .s-label {
            font-size: 0.75rem;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--gray);
            font-weight: 700;
            display: block;
            margin-bottom: 8px;
        }

        .stat-card .s-num {
            font-family: var(--font-display);
            font-size: 2rem;
            color: var(--dark);
            display: block;
        }

        .stat-card .s-icon {
            font-size: 1.5rem;
            float: right;
            opacity: 0.4;
        }

        .admin-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .admin-card-header {
            padding: 20px 28px;
            border-bottom: 1px solid var(--light-2);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .admin-card-header h2 {
            font-size: 1.1rem;
            color: var(--dark);
            font-family: var(--font-body);
            font-weight: 700;
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: var(--light);
            padding: 12px 16px;
            text-align: left;
            font-size: 0.75rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--text-light);
            font-weight: 700;
            font-family: var(--font-body);
        }

        td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--light-2);
            font-size: 0.9rem;
            color: var(--text);
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background: #fafafa;
        }

        .tour-thumb {
            width: 64px;
            height: 44px;
            object-fit: cover;
            border-radius: var(--radius);
        }

        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 10px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .action-btns {
            display: flex;
            gap: 6px;
        }

        /* ── Form Card ── */
        .admin-form-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
            padding: 36px 40px;
            max-width: 860px;
            width: 100%;
            box-sizing: border-box;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--dark);
        }

        .req {
            color: #e74c3c;
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #ddd;
            border-radius: var(--radius);
            box-sizing: border-box;
            font-family: inherit;
        }

        .form-hint {
            font-size: 0.8rem;
            color: var(--gray);
            margin-top: 4px;
            display: block;
        }

        .form-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
        }

        /* ── Image Upload Zone ── */
        .img-upload-zone {
            border: 2px dashed #ddd;
            border-radius: var(--radius);
            padding: 40px 20px;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            background: var(--light);
            width: 100%;
            box-sizing: border-box;
        }

        .img-upload-zone:hover {
            border-color: var(--gold);
            background: #fffbf0;
        }

        .img-upload-zone.dragover {
            border-color: #2ecc71;
            background: #d5f4e6;
        }

        .upload-placeholder .icon {
            font-size: 2.5rem;
            margin-bottom: 12px;
            display: block;
        }

        .upload-placeholder p {
            margin: 4px 0;
            font-size: 0.9rem;
            color: var(--text-light);
        }

        .img-preview {
            max-width: 100%;
            max-height: 280px;
            border-radius: var(--radius);
            display: none;
            margin: 0 auto;
        }

        .current-img {
            width: 100%;
            max-height: 220px;
            object-fit: cover;
            border-radius: var(--radius);
            margin-bottom: 12px;
            display: block;
        }

        @media(max-width:900px) {
            .admin-layout {
                grid-template-columns: 1fr;
            }

            .admin-sidebar {
                display: none;
            }

            .admin-main {
                padding: 20px;
            }

            .admin-stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .form-grid-2,
            .form-grid-3 {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="admin-layout">

        <aside class="admin-sidebar">
            <div class="admin-logo-wrap">
                <a href="index.php">VietNam <span>Travel</span></a>
                <small>Admin Panel</small>
            </div>
            <nav class="admin-nav">
                <a href="admin-tours.php" class="<?php echo $action === 'list'    ? 'active' : ''; ?>">🗺️ Tours</a>
                <a href="admin-tours.php?action=add" class="<?php echo $action === 'add'     ? 'active' : ''; ?>">➕ Add Tour</a>
                <a href="admin-tours.php?action=bookings" class="<?php echo $action === 'bookings' ? 'active' : ''; ?>">📋 Bookings</a>
                <a href="index.php">🌐 View Website</a>
            </nav>
        </aside>

        <main class="admin-main">

            <?php if ($msg):   ?><div class="alert alert-success"><?php echo $msg; ?></div><?php endif; ?>
            <?php if ($error): ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

            <?php
            // ══════════════════════════
            // BOOKINGS
            // ══════════════════════════
            if ($action === 'bookings'):
                $b_result = mysqli_query($conn, "SELECT b.*, t.title as tour_title FROM bookings b JOIN tours t ON b.tour_id=t.id ORDER BY b.created_at DESC");
            ?>
                <div class="admin-topbar">
                    <h1>📋 All Bookings</h1>
                    <a href="admin-tours.php" class="btn btn-outline btn-sm">← Back</a>
                </div>
                <div class="admin-card">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tour</th>
                                <th>Guest</th>
                                <th>Email</th>
                                <th>Travel Date</th>
                                <th>People</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Booked On</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($b = mysqli_fetch_assoc($b_result)): ?>
                                <tr>
                                    <td><?php echo str_pad($b['id'], 4, '0', STR_PAD_LEFT); ?></td>
                                    <td><?php echo htmlspecialchars($b['tour_title']); ?></td>
                                    <td><strong><?php echo htmlspecialchars($b['full_name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($b['email']); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($b['travel_date'])); ?></td>
                                    <td><?php echo $b['num_people']; ?></td>
                                    <td><strong><?php echo number_format($b['total_price'], 0, ',', '.'); ?> ₫</strong></td>
                                    <td><span class="status-badge status-<?php echo $b['status']; ?>"><?php echo ucfirst($b['status']); ?></span></td>
                                    <td><?php echo date('M j, Y', strtotime($b['created_at'])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

            <?php
            // ══════════════════════════
            // ADD TOUR
            // ══════════════════════════
            elseif ($action === 'add'):
            ?>
                <div class="admin-topbar">
                    <h1>➕ Add New Tour</h1>
                    <a href="admin-tours.php" class="btn btn-outline btn-sm">← Back</a>
                </div>
                <div class="admin-form-card">
                    <form method="POST" action="admin-tours.php?action=add" enctype="multipart/form-data" id="tourForm">

                        <div class="form-group">
                            <label>Tour Image <span class="req">*</span></label>
                            <div class="img-upload-zone" id="uploadZone">
                                <div class="upload-placeholder" id="uploadPlaceholder">
                                    <span class="icon">🖼️</span>
                                    <p><strong>Click to upload</strong> or drag and drop</p>
                                    <p style="font-size:0.8rem;color:var(--gray);">JPG, PNG, WEBP, GIF – Max 5MB</p>
                                </div>
                                <img id="imgPreview" class="img-preview" alt="Preview">
                                <input type="file" name="image" id="imageFile" accept="image/*" style="display:none" required>
                            </div>
                        </div>

                        <div class="form-grid-2">
                            <div class="form-group">
                                <label>Tour Title <span class="req">*</span></label>
                                <input type="text" name="title" class="form-control" placeholder="e.g. Ha Long Bay 5 Days" required oninput="autoSlug(this.value)">
                            </div>
                            <div class="form-group">
                                <label>Slug (URL)</label>
                                <input type="text" name="slug" id="slugField" class="form-control" placeholder="ha-long-bay-5-days">
                                <span class="form-hint">Auto-generated from title</span>
                            </div>
                        </div>

                        <div class="form-grid-3">
                            <div class="form-group">
                                <label>Category <span class="req">*</span></label>
                                <select name="category_id" class="form-control" required>
                                    <option value="">Select category...</option>
                                    <?php mysqli_data_seek($categories, 0);
                                    while ($cat = mysqli_fetch_assoc($categories)): ?>
                                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Destination <span class="req">*</span></label>
                                <input type="text" name="destination" class="form-control" placeholder="Ha Long, Quang Ninh" required>
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-grid-3">
                            <div class="form-group">
                                <label>Price (VND) <span class="req">*</span></label>
                                <input type="number" name="price" class="form-control" min="0" placeholder="9900000" required>
                            </div>
                            <div class="form-group">
                                <label>Duration (Days) <span class="req">*</span></label>
                                <input type="number" name="duration" class="form-control" min="1" placeholder="5" required>
                            </div>
                            <div class="form-group">
                                <label>Max People <span class="req">*</span></label>
                                <input type="number" name="max_people" class="form-control" min="1" placeholder="15" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Short Description <span class="req">*</span></label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Brief overview of the tour (1-2 sentences)..." required></textarea>
                        </div>

                        <div class="form-group">
                            <label>Full Itinerary</label>
                            <textarea name="content" class="form-control" rows="8" placeholder="Day 1: Depart from...&#10;Day 2: ...&#10;Day 3: ..."></textarea>
                        </div>

                        <div style="display:flex;gap:12px;margin-top:8px;">
                            <button type="submit" class="btn btn-gold">🚀 Publish Tour</button>
                            <a href="admin-tours.php" class="btn btn-outline">Cancel</a>
                        </div>
                    </form>
                </div>

            <?php
            // ══════════════════════════
            // EDIT TOUR
            // ══════════════════════════
            elseif ($action === 'edit' && $edit_tour):
            ?>
                <div class="admin-topbar">
                    <h1>✏️ Edit Tour</h1>
                    <div style="display:flex;gap:8px;">
                        <a href="tour-detail.php?id=<?php echo $tour_id; ?>" class="btn btn-outline btn-sm" target="_blank">👁 Preview</a>
                        <a href="admin-tours.php" class="btn btn-outline btn-sm">← Back</a>
                    </div>
                </div>
                <div class="admin-form-card">
                    <form method="POST" action="admin-tours.php?action=edit&id=<?php echo $tour_id; ?>" enctype="multipart/form-data">
                        <input type="hidden" name="old_image" value="<?php echo img_url($edit_tour['image_url']); ?>">

                        <div class="form-group">
                            <label>Tour Image</label>
                            <?php if (!empty($edit_tour['image_url'])): ?>
                                <img src="<?php echo img_url($edit_tour['image_url']); ?>" alt="Current" class="current-img">
                                <p class="form-hint" style="margin-bottom:12px;">Current image. Upload a new one to replace it.</p>
                            <?php endif; ?>
                            <div class="img-upload-zone" id="uploadZone">
                                <div class="upload-placeholder" id="uploadPlaceholder">
                                    <span class="icon">🔄</span>
                                    <p><strong>Click to change image</strong></p>
                                    <p style="font-size:0.8rem;color:var(--gray);">JPG, PNG, WEBP – Max 5MB</p>
                                </div>
                                <img id="imgPreview" class="img-preview" alt="Preview">
                                <input type="file" name="image" id="imageFile" accept="image/*" style="display:none">
                            </div>
                        </div>

                        <div class="form-grid-2">
                            <div class="form-group">
                                <label>Tour Title <span class="req">*</span></label>
                                <input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($edit_tour['title']); ?>">
                            </div>
                            <div class="form-group">
                                <label>Slug</label>
                                <input type="text" name="slug" class="form-control" value="<?php echo htmlspecialchars($edit_tour['slug']); ?>">
                            </div>
                        </div>

                        <div class="form-grid-3">
                            <div class="form-group">
                                <label>Category <span class="req">*</span></label>
                                <select name="category_id" class="form-control" required>
                                    <?php mysqli_data_seek($categories, 0);
                                    while ($cat = mysqli_fetch_assoc($categories)): ?>
                                        <option value="<?php echo $cat['id']; ?>" <?php echo $cat['id'] == $edit_tour['category_id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Destination <span class="req">*</span></label>
                                <input type="text" name="destination" class="form-control" required value="<?php echo htmlspecialchars($edit_tour['destination']); ?>">
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="active" <?php echo $edit_tour['status'] === 'active'  ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo $edit_tour['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-grid-3">
                            <div class="form-group">
                                <label>Price (VND)</label>
                                <input type="number" name="price" class="form-control" value="<?php echo $edit_tour['price']; ?>">
                            </div>
                            <div class="form-group">
                                <label>Duration (Days)</label>
                                <input type="number" name="duration" class="form-control" value="<?php echo $edit_tour['duration']; ?>">
                            </div>
                            <div class="form-group">
                                <label>Max People</label>
                                <input type="number" name="max_people" class="form-control" value="<?php echo $edit_tour['max_people']; ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Short Description <span class="req">*</span></label>
                            <textarea name="description" class="form-control" rows="3" required><?php echo htmlspecialchars($edit_tour['description']); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Full Itinerary</label>
                            <textarea name="content" class="form-control" rows="8"><?php echo htmlspecialchars($edit_tour['content']); ?></textarea>
                        </div>

                        <div style="display:flex;gap:12px;flex-wrap:wrap;">
                            <button type="submit" class="btn btn-gold">💾 Save Changes</button>
                            <a href="tour-detail.php?id=<?php echo $tour_id; ?>" class="btn btn-outline" target="_blank">👁 Preview</a>
                            <a href="admin-tours.php?action=delete&id=<?php echo $tour_id; ?>" class="btn btn-dark"
                                onclick="return confirm('Delete this tour? This action cannot be undone.');">🗑 Delete</a>
                        </div>
                    </form>
                </div>

            <?php
            // ══════════════════════════
            // TOUR LIST
            // ══════════════════════════
            else:
            ?>
                <div class="admin-topbar">
                    <h1>🗺️ Tour Management</h1>
                    <a href="admin-tours.php?action=add" class="btn btn-gold btn-sm">+ Add New Tour</a>
                </div>

                <div class="admin-stats">
                    <div class="stat-card"><span class="s-icon">🗺️</span><span class="s-label">Total Tours</span><span class="s-num"><?php echo $stats['tours']; ?></span></div>
                    <div class="stat-card"><span class="s-icon">✅</span><span class="s-label">Active Tours</span><span class="s-num"><?php echo $stats['active']; ?></span></div>
                    <div class="stat-card"><span class="s-icon">📋</span><span class="s-label">Total Bookings</span><span class="s-num"><?php echo $stats['bookings']; ?></span></div>
                    <div class="stat-card"><span class="s-icon">⏳</span><span class="s-label">Pending</span><span class="s-num"><?php echo $stats['pending']; ?></span></div>
                </div>

                <div class="admin-card">
                    <div class="admin-card-header">
                        <h2>All Tours</h2>
                        <form method="GET" style="display:flex;gap:8px;">
                            <input type="text" name="s" class="form-control" style="width:220px;" placeholder="Search title, destination..." value="<?php echo htmlspecialchars($s); ?>">
                            <button type="submit" class="btn btn-gold btn-sm">Search</button>
                            <?php if ($s): ?><a href="admin-tours.php" class="btn btn-outline btn-sm">Clear</a><?php endif; ?>
                        </form>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Destination</th>
                                <th>Price</th>
                                <th>Days</th>
                                <th>Views</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($t = mysqli_fetch_assoc($tours_list)): ?>
                                <tr>
                                    <td><img src="<?php echo img_url($t['image_url']); ?>" alt="" class="tour-thumb"></td>
                                    <td><strong><?php echo htmlspecialchars($t['title']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($t['cat_name']); ?></td>
                                    <td><?php echo htmlspecialchars($t['destination']); ?></td>
                                    <td><?php echo number_format($t['price'], 0, ',', '.'); ?> ₫</td>
                                    <td><?php echo $t['duration']; ?></td>
                                    <td><?php echo number_format($t['views']); ?></td>
                                    <td><span class="status-badge status-<?php echo $t['status']; ?>"><?php echo ucfirst($t['status']); ?></span></td>
                                    <td>
                                        <div class="action-btns">
                                            <a href="tour-detail.php?id=<?php echo $t['id']; ?>" class="btn btn-outline btn-sm" target="_blank">👁</a>
                                            <a href="admin-tours.php?action=edit&id=<?php echo $t['id']; ?>" class="btn btn-gold btn-sm">Edit</a>
                                            <a href="admin-tours.php?action=delete&id=<?php echo $t['id']; ?>" class="btn btn-dark btn-sm" onclick="return confirm('Delete this tour?');">Del</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <?php if ($total_pages > 1): ?>
                        <div class="pagination" style="padding:20px;">
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <?php if ($i == $current_page): ?><span class="page-link active"><?php echo $i; ?></span>
                                <?php else: ?><a href="?page=<?php echo $i; ?><?php echo $s ? '&s=' . urlencode($s) : ''; ?>" class="page-link"><?php echo $i; ?></a><?php endif; ?>
                            <?php endfor; ?>
                        </div>
                    <?php endif; ?>
                </div>

            <?php endif; ?>
        </main>
    </div>

    <script>
        function autoSlug(title) {
            var slug = title.toLowerCase()
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                .replace(/[đĐ]/g, 'd')
                .replace(/[^a-z0-9\s-]/g, '')
                .trim().replace(/\s+/g, '-');
            var f = document.getElementById('slugField');
            if (f) f.value = slug;
        }

        var fileInput = document.getElementById('imageFile');
        if (fileInput) {
            // Click on zone triggers file input
            var zone = document.getElementById('uploadZone');
            if (zone) {
                zone.addEventListener('click', function(e) {
                    if (e.target !== fileInput) fileInput.click();
                });
                zone.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    zone.classList.add('dragover');
                });
                zone.addEventListener('dragleave', function() {
                    zone.classList.remove('dragover');
                });
                zone.addEventListener('drop', function(e) {
                    e.preventDefault();
                    zone.classList.remove('dragover');
                    if (e.dataTransfer.files.length > 0) {
                        // DataTransfer workaround
                        var dt = e.dataTransfer;
                        fileInput.files = dt.files;
                        showPreview(dt.files[0]);
                    }
                });
            }
            fileInput.addEventListener('change', function(e) {
                if (e.target.files.length > 0) showPreview(e.target.files[0]);
            });
        }

        function showPreview(file) {
            var allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowed.includes(file.type)) {
                alert('Invalid file type. Please use JPG, PNG, GIF or WEBP.');
                return;
            }
            if (file.size > 5242880) {
                alert('File too large. Maximum size is 5MB.');
                return;
            }
            var reader = new FileReader();
            reader.onload = function(ev) {
                var preview = document.getElementById('imgPreview');
                var placeholder = document.getElementById('uploadPlaceholder');
                if (preview) {
                    preview.src = ev.target.result;
                    preview.style.display = 'block';
                }
                if (placeholder) placeholder.style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
    </script>
</body>

</html>
<?php mysqli_close($conn); ?>
<?php
include 'includes/db-connect.php';
$slug = isset($_GET['slug']) ? mysqli_real_escape_string($conn, trim($_GET['slug'])) : '';
$cat_result = mysqli_query($conn, "SELECT * FROM categories WHERE slug = '$slug'");
if (mysqli_num_rows($cat_result) == 0) { header("Location: tours.php"); exit; }
$category = mysqli_fetch_assoc($cat_result);
$per_page = 6; $current_page = max(1, isset($_GET['page'])?(int)$_GET['page']:1); $offset = ($current_page-1)*$per_page;
$total = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as total FROM tours WHERE category_id={$category['id']} AND status='active'"))['total'];
$total_pages = max(1, ceil($total/$per_page));
$result   = mysqli_query($conn,"SELECT t.*, c.name as cat_name FROM tours t JOIN categories c ON t.category_id=c.id WHERE t.category_id={$category['id']} AND t.status='active' ORDER BY t.views DESC LIMIT $per_page OFFSET $offset");
$all_cats = mysqli_query($conn,"SELECT c.*, COUNT(t.id) as tc FROM categories c LEFT JOIN tours t ON c.id=t.category_id AND t.status='active' GROUP BY c.id ORDER BY c.name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($category['name']); ?> Tours – VietNam Travel</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .category-hero { background:var(--dark); padding:140px 0 80px; text-align:center; position:relative; overflow:hidden; }
        .category-hero::before { content:''; position:absolute; inset:0; background:linear-gradient(135deg,rgba(201,168,76,0.1) 0%,transparent 60%); }
        .category-icon-lg { font-size:4rem; display:block; margin-bottom:16px; }
        .category-hero h1 { color:var(--white); } .category-hero p { color:rgba(255,255,255,0.6); max-width:520px; margin:0 auto 24px; }
        .tour-count-badge { display:inline-block; background:rgba(201,168,76,0.15); border:1px solid rgba(201,168,76,0.3); color:var(--gold); padding:6px 18px; border-radius:20px; font-size:0.82rem; font-weight:700; letter-spacing:1px; }
        .cat-switcher { background:var(--white); padding:20px 0; box-shadow:0 2px 12px rgba(0,0,0,0.06); overflow-x:auto; }
        .cat-switcher-inner { display:flex; gap:10px; padding:0 24px; min-width:max-content; max-width:1260px; margin:0 auto; }
        .cat-btn { display:inline-flex; align-items:center; gap:8px; padding:9px 18px; border-radius:20px; font-size:0.83rem; font-weight:600; color:var(--text); background:var(--light); border:1px solid var(--light-2); transition:var(--transition); white-space:nowrap; }
        .cat-btn:hover, .cat-btn.active { background:var(--gold); border-color:var(--gold); color:var(--dark); }
        .cat-btn-count { background:rgba(0,0,0,0.1); border-radius:10px; padding:1px 7px; font-size:0.75rem; }
        .empty-state { text-align:center; padding:100px 20px; }
        .empty-state .icon { font-size:3.5rem; margin-bottom:20px; }
    </style>
</head>
<body>
<?php include 'includes/navbar.php'; ?>
<div class="category-hero">
    <div class="container" style="position:relative;z-index:1;">
        <span class="category-icon-lg"><?php echo $category['icon']; ?></span>
        <span class="section-label">Curated Collection</span>
        <h1><?php echo htmlspecialchars($category['name']); ?></h1>
        <p><?php echo htmlspecialchars($category['description']); ?></p>
        <span class="tour-count-badge"><?php echo $total; ?> Tour<?php echo $total!=1?'s':''; ?> Available</span>
    </div>
</div>
<div class="cat-switcher">
    <div class="cat-switcher-inner">
        <a href="tours.php" class="cat-btn">🌍 All Tours</a>
        <?php while ($cat = mysqli_fetch_assoc($all_cats)): ?>
        <a href="category.php?slug=<?php echo $cat['slug']; ?>" class="cat-btn <?php echo $cat['slug']===$slug?'active':''; ?>">
            <?php echo $cat['icon']; ?> <?php echo htmlspecialchars($cat['name']); ?>
            <span class="cat-btn-count"><?php echo $cat['tc']; ?></span>
        </a>
        <?php endwhile; ?>
    </div>
</div>
<section class="section" style="padding-top:50px;">
    <div class="container">
        <?php if ($total > 0): ?>
        <div style="margin-bottom:28px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
            <p style="color:var(--text-light);font-size:0.9rem;margin:0;">Showing <strong style="color:var(--dark);"><?php echo mysqli_num_rows($result); ?></strong> of <strong style="color:var(--dark);"><?php echo $total; ?></strong> tours</p>
            <a href="tours.php?category=<?php echo $category['id']; ?>" style="font-size:0.85rem;color:var(--gold);">Filter further →</a>
        </div>
        <div class="tours-grid">
            <?php while ($tour = mysqli_fetch_assoc($result)): ?>
            <div class="tour-card">
                <div class="tour-card-image">
                    <img src="<?php echo img_url($tour['image_url']); ?>" alt="<?php echo htmlspecialchars($tour['title']); ?>" loading="lazy">
                    <span class="tour-card-badge"><?php echo htmlspecialchars($tour['cat_name']); ?></span>
                </div>
                <div class="tour-card-body">
                    <div class="tour-card-destination">📍 <?php echo htmlspecialchars($tour['destination']); ?></div>
                    <h3 class="tour-card-title"><?php echo htmlspecialchars($tour['title']); ?></h3>
                    <p class="tour-card-excerpt"><?php echo substr(htmlspecialchars($tour['description']),0,110).'...'; ?></p>
                    <div class="tour-card-meta">
                        <div class="tour-meta-item"><span class="icon">🕐</span><?php echo $tour['duration']; ?> Days</div>
                        <div class="tour-meta-item"><span class="icon">👥</span>Max <?php echo $tour['max_people']; ?></div>
                        <div class="tour-meta-item"><span class="icon">👁</span><?php echo number_format($tour['views']); ?></div>
                    </div>
                    <div class="tour-card-footer">
                        <div class="tour-price">
                            <span class="from">From</span>
                            <span class="amount"><?php echo number_format($tour['price'],0,',','.'); ?> ₫</span>
                            <span class="per">/ person</span>
                        </div>
                        <a href="tour-detail.php?id=<?php echo $tour['id']; ?>" class="btn btn-gold btn-sm">View Tour</a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($current_page>1): ?><a href="?slug=<?php echo $slug; ?>&page=<?php echo $current_page-1; ?>" class="page-link wide">← Prev</a><?php else: ?><span class="page-link wide disabled">← Prev</span><?php endif; ?>
            <?php for($i=1;$i<=$total_pages;$i++): ?><?php if($i==$current_page): ?><span class="page-link active"><?php echo $i; ?></span><?php else: ?><a href="?slug=<?php echo $slug; ?>&page=<?php echo $i; ?>" class="page-link"><?php echo $i; ?></a><?php endif; ?><?php endfor; ?>
            <?php if ($current_page<$total_pages): ?><a href="?slug=<?php echo $slug; ?>&page=<?php echo $current_page+1; ?>" class="page-link wide">Next →</a><?php else: ?><span class="page-link wide disabled">Next →</span><?php endif; ?>
        </div>
        <?php endif; ?>
        <?php else: ?>
        <div class="empty-state">
            <div class="icon">🗺️</div>
            <h3>No tours in this category yet</h3>
            <p>We are adding new tours soon. Explore our other categories in the meantime!</p>
            <a href="tours.php" class="btn btn-gold">Browse All Tours</a>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
</body>
</html>
<?php mysqli_close($conn); ?>

<?php
include 'includes/db-connect.php';
$search    = isset($_GET['search'])    ? trim($_GET['search'])    : '';
$cat_id    = isset($_GET['category'])  ? (int)$_GET['category']   : 0;
$duration  = isset($_GET['duration'])  ? trim($_GET['duration'])   : '';
$sort      = isset($_GET['sort'])      ? trim($_GET['sort'])       : 'newest';
$min_price = isset($_GET['min_price']) ? (int)$_GET['min_price']   : 0;
$max_price = isset($_GET['max_price']) ? (int)$_GET['max_price']   : 0;
$per_page     = 6;
$current_page = max(1, isset($_GET['page']) ? (int)$_GET['page'] : 1);
$where = "WHERE t.status = 'active'";
if (!empty($search)) { $s = mysqli_real_escape_string($conn, $search); $where .= " AND (t.title LIKE '%$s%' OR t.destination LIKE '%$s%' OR t.description LIKE '%$s%')"; }
if ($cat_id > 0)       $where .= " AND t.category_id = $cat_id";
if ($duration === '1-5')  $where .= " AND t.duration BETWEEN 1 AND 5";
if ($duration === '6-10') $where .= " AND t.duration BETWEEN 6 AND 10";
if ($duration === '11+')  $where .= " AND t.duration >= 11";
if ($min_price > 0)    $where .= " AND t.price >= $min_price";
if ($max_price > 0)    $where .= " AND t.price <= $max_price";
$order = "t.created_at DESC";
if ($sort === 'price_asc')  $order = "t.price ASC";
if ($sort === 'price_desc') $order = "t.price DESC";
if ($sort === 'popular')    $order = "t.views DESC";
if ($sort === 'duration')   $order = "t.duration ASC";
$total_row   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tours t JOIN categories c ON t.category_id = c.id $where"));
$total       = $total_row['total'];
$total_pages = max(1, ceil($total / $per_page));
$offset      = ($current_page - 1) * $per_page;
$result      = mysqli_query($conn, "SELECT t.*, c.name as cat_name FROM tours t JOIN categories c ON t.category_id = c.id $where ORDER BY $order LIMIT $per_page OFFSET $offset");
$cat_result  = mysqli_query($conn, "SELECT id, name, icon FROM categories ORDER BY name");
function buildQuery($extra = []) { $base = $_GET; unset($base['page']); return http_build_query(array_merge($base, $extra)); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Tours – VietNam Travel</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .tours-layout { display:grid; grid-template-columns:280px 1fr; gap:40px; align-items:start; }
        .filter-sidebar { background:var(--white); border-radius:var(--radius-lg); padding:32px; box-shadow:var(--shadow); position:sticky; top:100px; }
        .filter-sidebar h3 { font-size:1rem; letter-spacing:2px; text-transform:uppercase; font-family:var(--font-body); font-weight:700; color:var(--dark); margin-bottom:24px; padding-bottom:16px; border-bottom:2px solid var(--gold); }
        .filter-section { margin-bottom:28px; }
        .filter-section-title { font-size:0.78rem; letter-spacing:1.5px; text-transform:uppercase; font-weight:700; color:var(--text-light); margin-bottom:14px; }
        .category-filter-list { list-style:none; }
        .category-filter-list li { margin-bottom:8px; }
        .category-filter-list a { display:flex; align-items:center; gap:10px; padding:9px 14px; border-radius:var(--radius); color:var(--text); font-size:0.9rem; font-weight:500; transition:var(--transition); border:1px solid transparent; }
        .category-filter-list a:hover, .category-filter-list a.active { background:rgba(201,168,76,0.08); border-color:var(--gold); color:var(--gold-dark); }
        .cat-count-badge { margin-left:auto; background:var(--light-2); color:var(--text-light); font-size:0.72rem; padding:2px 8px; border-radius:10px; font-weight:600; }
        .results-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:28px; flex-wrap:wrap; gap:12px; }
        .results-count  { font-size:0.9rem; color:var(--text-light); }
        .results-count strong { color:var(--dark); }
        .active-filters { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:20px; }
        .filter-tag { display:inline-flex; align-items:center; gap:6px; background:rgba(201,168,76,0.1); border:1px solid var(--gold); color:var(--gold-dark); padding:5px 12px; border-radius:20px; font-size:0.8rem; font-weight:600; }
        .filter-tag a { color:var(--gold-dark); }
        .no-results { text-align:center; padding:80px 20px; background:var(--white); border-radius:var(--radius-lg); box-shadow:var(--shadow); }
        .no-results .icon { font-size:3rem; margin-bottom:16px; }
        @media(max-width:960px){ .tours-layout { grid-template-columns:1fr; } .filter-sidebar { position:static; } }
    </style>
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="page-hero">
    <div class="container">
        <span class="section-label">Curated Journeys</span>
        <h1>Explore All Tours</h1>
        <p>Discover <?php echo $total; ?> extraordinary travel experiences across Vietnam</p>
    </div>
</div>

<div style="background:var(--white);padding:24px 0;box-shadow:0 2px 12px rgba(0,0,0,0.06);">
    <div class="container">
        <form method="GET" action="tours.php" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
            <div class="form-group" style="margin:0;flex:1;min-width:200px;">
                <label>Search</label>
                <input type="text" name="search" class="form-control" placeholder="Destination, tour name..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="form-group" style="margin:0;min-width:150px;">
                <label>Duration</label>
                <select name="duration" class="form-control">
                    <option value="">Any Duration</option>
                    <option value="1-5"  <?php echo $duration==='1-5'  ?'selected':''; ?>>1–5 Days</option>
                    <option value="6-10" <?php echo $duration==='6-10' ?'selected':''; ?>>6–10 Days</option>
                    <option value="11+"  <?php echo $duration==='11+'  ?'selected':''; ?>>11+ Days</option>
                </select>
            </div>
            <div class="form-group" style="margin:0;min-width:130px;">
                <label>Min Price (₫)</label>
                <input type="number" name="min_price" class="form-control" placeholder="0" value="<?php echo $min_price ?: ''; ?>">
            </div>
            <div class="form-group" style="margin:0;min-width:130px;">
                <label>Max Price (₫)</label>
                <input type="number" name="max_price" class="form-control" placeholder="Any" value="<?php echo $max_price ?: ''; ?>">
            </div>
            <?php if ($cat_id): ?><input type="hidden" name="category" value="<?php echo $cat_id; ?>"><?php endif; ?>
            <button type="submit" class="btn btn-gold">Search</button>
            <?php if (!empty($search) || $cat_id || $duration || $min_price || $max_price): ?>
            <a href="tours.php" class="btn btn-outline">Clear</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<section class="section" style="padding-top:50px;">
    <div class="container">
        <div class="tours-layout">
            <aside class="filter-sidebar">
                <h3>Filter Tours</h3>
                <div class="filter-section">
                    <div class="filter-section-title">Category</div>
                    <ul class="category-filter-list">
                        <li>
                            <a href="tours.php?<?php echo buildQuery(['category'=>'']); ?>" class="<?php echo !$cat_id?'active':''; ?>">
                                🌍 All Categories <span class="cat-count-badge"><?php echo $total; ?></span>
                            </a>
                        </li>
                        <?php
                        mysqli_data_seek($cat_result, 0);
                        while ($cat = mysqli_fetch_assoc($cat_result)):
                            $cn = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as n FROM tours WHERE category_id={$cat['id']} AND status='active'"));
                        ?>
                        <li>
                            <a href="tours.php?<?php echo buildQuery(['category'=>$cat['id']]); ?>" class="<?php echo $cat_id==$cat['id']?'active':''; ?>">
                                <?php echo $cat['icon']; ?> <?php echo htmlspecialchars($cat['name']); ?>
                                <span class="cat-count-badge"><?php echo $cn['n']; ?></span>
                            </a>
                        </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
                <div class="filter-section">
                    <div class="filter-section-title">Sort By</div>
                    <form method="GET" id="sortForm">
                        <?php if($search):?><input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>"><?php endif; ?>
                        <?php if($cat_id):?><input type="hidden" name="category" value="<?php echo $cat_id; ?>"><?php endif; ?>
                        <?php if($duration):?><input type="hidden" name="duration" value="<?php echo htmlspecialchars($duration); ?>"><?php endif; ?>
                        <select name="sort" class="form-control" onchange="document.getElementById('sortForm').submit()">
                            <option value="newest"     <?php echo $sort==='newest'    ?'selected':''; ?>>Newest First</option>
                            <option value="popular"    <?php echo $sort==='popular'   ?'selected':''; ?>>Most Popular</option>
                            <option value="price_asc"  <?php echo $sort==='price_asc' ?'selected':''; ?>>Price: Low to High</option>
                            <option value="price_desc" <?php echo $sort==='price_desc'?'selected':''; ?>>Price: High to Low</option>
                            <option value="duration"   <?php echo $sort==='duration'  ?'selected':''; ?>>Shortest Duration</option>
                        </select>
                    </form>
                </div>
            </aside>

            <div>
                <div class="results-header">
                    <p class="results-count">
                        Showing <strong><?php echo min($per_page, max(0,$total-$offset)); ?></strong> of <strong><?php echo $total; ?></strong> tours
                        <?php if (!empty($search)): ?>for "<strong><?php echo htmlspecialchars($search); ?></strong>"<?php endif; ?>
                    </p>
                    <span style="font-size:0.85rem;color:var(--text-light);">Page <?php echo $current_page; ?> of <?php echo $total_pages; ?></span>
                </div>

                <?php if (!empty($search) || $cat_id || $duration): ?>
                <div class="active-filters">
                    <?php if (!empty($search)): ?><span class="filter-tag">🔍 "<?php echo htmlspecialchars($search); ?>" <a href="tours.php?<?php echo buildQuery(['search'=>'']); ?>">×</a></span><?php endif; ?>
                    <?php if ($duration): ?><span class="filter-tag">🕐 <?php echo htmlspecialchars($duration); ?> Days <a href="tours.php?<?php echo buildQuery(['duration'=>'']); ?>">×</a></span><?php endif; ?>
                </div>
                <?php endif; ?>

                <?php if ($total > 0): ?>
                <div class="tours-grid">
                    <?php while ($tour = mysqli_fetch_assoc($result)): ?>
                    <div class="tour-card">
                        <div class="tour-card-image">
                            <img src="<?php echo htmlspecialchars($tour['image_url']); ?>" alt="<?php echo htmlspecialchars($tour['title']); ?>" loading="lazy">
                            <span class="tour-card-badge"><?php echo htmlspecialchars($tour['cat_name']); ?></span>
                        </div>
                        <div class="tour-card-body">
                            <div class="tour-card-destination">📍 <?php echo htmlspecialchars($tour['destination']); ?></div>
                            <h3 class="tour-card-title"><?php echo htmlspecialchars($tour['title']); ?></h3>
                            <p class="tour-card-excerpt"><?php echo substr(htmlspecialchars($tour['description']), 0, 110) . '...'; ?></p>
                            <div class="tour-card-meta">
                                <div class="tour-meta-item"><span class="icon">🕐</span><?php echo $tour['duration']; ?> Days</div>
                                <div class="tour-meta-item"><span class="icon">👥</span>Max <?php echo $tour['max_people']; ?></div>
                                <div class="tour-meta-item"><span class="icon">👁</span><?php echo number_format($tour['views']); ?></div>
                            </div>
                            <div class="tour-card-footer">
                                <div class="tour-price">
                                    <span class="from">From</span>
                                    <span class="amount"><?php echo number_format($tour['price'], 0, ',', '.'); ?> ₫</span>
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
                    <?php if ($current_page > 1): ?><a href="?<?php echo buildQuery(['page'=>$current_page-1]); ?>" class="page-link wide">← Prev</a><?php else: ?><span class="page-link wide disabled">← Prev</span><?php endif; ?>
                    <?php for ($i=1;$i<=$total_pages;$i++): ?>
                    <?php if ($i==$current_page): ?><span class="page-link active"><?php echo $i; ?></span><?php else: ?><a href="?<?php echo buildQuery(['page'=>$i]); ?>" class="page-link"><?php echo $i; ?></a><?php endif; ?>
                    <?php endfor; ?>
                    <?php if ($current_page < $total_pages): ?><a href="?<?php echo buildQuery(['page'=>$current_page+1]); ?>" class="page-link wide">Next →</a><?php else: ?><span class="page-link wide disabled">Next →</span><?php endif; ?>
                </div>
                <?php endif; ?>
                <?php else: ?>
                <div class="no-results">
                    <div class="icon">🔍</div>
                    <h3>No tours found</h3>
                    <p>Try adjusting your search or filter criteria.</p>
                    <a href="tours.php" class="btn btn-gold">View All Tours</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
</body>
</html>
<?php mysqli_close($conn); ?>

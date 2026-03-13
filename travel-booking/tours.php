<?php
$pageTitle = 'Tất Cả Tour';
require_once 'includes/header.php';

// ── Pagination config ──
$perPage = 6;
$page    = max(1, intval($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;

// ── Filters ──
$search    = trim($_GET['search']    ?? '');
$catFilter = intval($_GET['category'] ?? 0);
$priceMax  = intval($_GET['price_max']?? 0);
$sort      = $_GET['sort'] ?? 'newest';

// ── Build WHERE clause ──
$where = ["t.status = 'active'"];
$params = [];
$types  = '';

if ($search) {
    $like = '%' . $conn->real_escape_string($search) . '%';
    $where[] = "(t.name LIKE '$like' OR t.destination LIKE '$like' OR t.description LIKE '$like')";
}
if ($catFilter) {
    $where[] = "t.category_id = $catFilter";
}
if ($priceMax) {
    $where[] = "t.price <= $priceMax";
}
$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// ── Sort ──
$orderSQL = match($sort) {
    'price_asc'  => 'ORDER BY t.price ASC',
    'price_desc' => 'ORDER BY t.price DESC',
    'popular'    => 'ORDER BY t.current_bookings DESC',
    'duration'   => 'ORDER BY t.duration_days ASC',
    default      => 'ORDER BY t.id DESC'
};

// ── Count total ──
$countRes = $conn->query("SELECT COUNT(*) FROM tours t $whereSQL");
$total    = $countRes->fetch_row()[0];
$totalPages = max(1, ceil($total / $perPage));
$page = min($page, $totalPages);

// ── Fetch tours ──
$toursRes = $conn->query("
    SELECT t.*, c.name AS cat_name,
           COALESCE(AVG(r.rating),0) AS avg_rating,
           COUNT(DISTINCT r.id) AS review_count
    FROM tours t
    JOIN categories c ON c.id = t.category_id
    LEFT JOIN reviews r ON r.tour_id = t.id
    $whereSQL
    GROUP BY t.id
    $orderSQL
    LIMIT $perPage OFFSET $offset
");

// ── Categories for chips ──
$catsRes = $conn->query("SELECT * FROM categories ORDER BY id");
?>

<!-- Page Hero -->
<div class="page-hero">
    <h1>🗺 Tất Cả Tour Du Lịch</h1>
    <p>Khám phá <?= $total ?> tour hấp dẫn – tìm chuyến đi hoàn hảo cho bạn</p>
</div>

<!-- Filter Bar -->
<div class="filter-bar">
    <div class="container">
        <div class="filter-inner">
            <span class="filter-label">Danh mục:</span>
            <div class="filter-chips">
                <button class="chip <?= !$catFilter ? 'active' : '' ?>" data-category="all">🌏 Tất Cả</button>
                <?php $catsRes->data_seek(0); while ($c = $catsRes->fetch_assoc()): ?>
                    <button class="chip <?= $catFilter == $c['id'] ? 'active' : '' ?>" data-category="<?= $c['id'] ?>">
                        <?= $c['icon'] ?> <?= htmlspecialchars($c['name']) ?>
                    </button>
                <?php endwhile; ?>
            </div>
            <div style="margin-left:auto;display:flex;gap:10px;align-items:center;">
                <input type="text" id="searchLive" class="search-input" placeholder="🔍 Tìm nhanh..."
                       value="<?= htmlspecialchars($search) ?>" style="width:200px;padding:8px 14px;font-size:.88rem;">
                <select id="sortSelect" class="filter-select">
                    <option value="newest"     <?= $sort=='newest'     ?'selected':'' ?>>Mới Nhất</option>
                    <option value="price_asc"  <?= $sort=='price_asc'  ?'selected':'' ?>>Giá Tăng Dần</option>
                    <option value="price_desc" <?= $sort=='price_desc' ?'selected':'' ?>>Giá Giảm Dần</option>
                    <option value="popular"    <?= $sort=='popular'    ?'selected':'' ?>>Phổ Biến</option>
                    <option value="duration"   <?= $sort=='duration'   ?'selected':'' ?>>Thời Gian Ngắn</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Tours Grid -->
<section class="tours-section" style="padding-top:0;">
    <div class="container">
        <?php if ($toursRes && $toursRes->num_rows > 0): ?>
        <div class="tours-grid">
        <?php while ($tour = $toursRes->fetch_assoc()):
            $pct  = $tour['max_capacity'] > 0 ? round($tour['current_bookings']/$tour['max_capacity']*100) : 0;
            $left = $tour['max_capacity'] - $tour['current_bookings'];
            $img  = $tour['image'] ? BASE_URL . '/uploads/'.$tour['image'] : 'https://picsum.photos/seed/'.$tour['id'].'extra/640/400';
        ?>
        <div class="tour-card">
            <div class="tour-img-wrap">
                <img src="<?= $img ?>" alt="<?= htmlspecialchars($tour['name']) ?>" loading="lazy">
                <div class="tour-img-overlay"></div>
                <span class="tour-badge badge badge-coral"><?= htmlspecialchars($tour['cat_name']) ?></span>
                <span class="tour-price-badge"><?= formatPrice($tour['price']) ?></span>
            </div>
            <div class="tour-body">
                <div class="tour-dest">📍 <?= htmlspecialchars($tour['destination']) ?></div>
                <h3 class="tour-title"><a href="tour-detail.php?id=<?= $tour['id'] ?>"><?= htmlspecialchars($tour['name']) ?></a></h3>
                <p style="font-size:.88rem;color:var(--mid);margin-bottom:12px;"><?= mb_substr(strip_tags($tour['description']),0,100) ?>...</p>
                <div class="tour-meta">
                    <span>📅 <?= $tour['duration_days'] ?> ngày</span>
                    <span>👥 Còn <?= max(0,$left) ?> chỗ</span>
                    <?php if($tour['departure_date']): ?>
                        <span>🚀 <?= date('d/m/Y', strtotime($tour['departure_date'])) ?></span>
                    <?php endif; ?>
                </div>
                <div class="capacity-bar"><div class="capacity-fill" style="width:<?= $pct ?>%"></div></div>
                <div class="tour-footer">
                    <div class="tour-rating">
                        <span class="stars"><?= $tour['avg_rating']>0 ? str_repeat('★',round($tour['avg_rating'])).str_repeat('☆',5-round($tour['avg_rating'])) : '☆☆☆☆☆' ?></span>
                        <span style="color:var(--mid);font-size:.82rem">(<?= $tour['review_count'] ?>)</span>
                    </div>
                    <a href="tour-detail.php?id=<?= $tour['id'] ?>" class="btn btn-primary btn-sm">Chi Tiết</a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php
            $buildURL = function($p) use ($search, $catFilter, $priceMax, $sort) {
                $q = http_build_query(array_filter(['search'=>$search,'category'=>$catFilter,'price_max'=>$priceMax,'sort'=>$sort,'page'=>$p]));
                return 'tours.php?' . $q;
            };
            ?>
            <a href="<?= $buildURL(max(1,$page-1)) ?>" class="page-btn <?= $page==1?'disabled':'' ?>">‹</a>
            <?php for($i=1;$i<=$totalPages;$i++): ?>
                <a href="<?= $buildURL($i) ?>" class="page-btn <?= $i==$page?'active':'' ?>"><?= $i ?></a>
            <?php endfor; ?>
            <a href="<?= $buildURL(min($totalPages,$page+1)) ?>" class="page-btn <?= $page==$totalPages?'disabled':'' ?>">›</a>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <div class="no-results">
            <div class="icon">🔍</div>
            <h3>Không tìm thấy tour nào</h3>
            <p>Thử tìm kiếm với từ khóa khác hoặc xóa bộ lọc.</p>
            <a href="tours.php" class="btn btn-primary" style="margin-top:16px;">Xem Tất Cả Tour</a>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>

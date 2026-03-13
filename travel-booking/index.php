<?php
$pageTitle = 'Trang Chủ';
require_once 'includes/header.php';

// Fetch categories
$cats = $conn->query("SELECT * FROM categories ORDER BY id");

// Fetch 6 featured tours
$featured = $conn->query("
    SELECT t.*, c.name AS cat_name,
           COALESCE(AVG(r.rating),0) AS avg_rating,
           COUNT(DISTINCT r.id) AS review_count
    FROM tours t
    JOIN categories c ON c.id = t.category_id
    LEFT JOIN reviews r ON r.tour_id = t.id
    WHERE t.status = 'active'
    GROUP BY t.id
    ORDER BY t.id DESC LIMIT 6
");

// Fetch categories for search
$catsSearch = $conn->query("SELECT * FROM categories ORDER BY id");

// Stats
$totalTours    = $conn->query("SELECT COUNT(*) FROM tours WHERE status='active'")->fetch_row()[0];
$totalBookings = $conn->query("SELECT COUNT(*) FROM bookings")->fetch_row()[0];
?>

<!-- HERO -->
<section class="hero">
    <div class="hero-content">
        <div class="hero-tag">🌏 Khám Phá Việt Nam & Thế Giới</div>
        <h1>Hành Trình Của Bạn Bắt Đầu <em>Từ Đây</em></h1>
        <p class="hero-sub">Hơn <?= $totalTours ?> tour chất lượng cao – từ biển đảo nhiệt đới đến núi rừng hùng vĩ. Đặt tour dễ dàng, giá tốt nhất.</p>
        <div class="hero-btns">
            <a href="tours.php" class="btn btn-primary">🔍 Xem Tất Cả Tour</a>
            <a href="tours.php?category=5" class="btn btn-outline" style="color:#fff;border-color:rgba(255,255,255,.5)">✈ Tour Nước Ngoài</a>
        </div>
        <div class="hero-stats">
            <div class="stat"><div class="stat-num"><?= $totalTours ?>+</div><div class="stat-label">Tour Hấp Dẫn</div></div>
            <div class="stat"><div class="stat-num"><?= $totalBookings ?>+</div><div class="stat-label">Lượt Đặt Tour</div></div>
            <div class="stat"><div class="stat-num">5★</div><div class="stat-label">Đánh Giá</div></div>
        </div>
    </div>
</section>

<!-- SEARCH -->
<section class="search-section">
    <form class="search-form container" action="tours.php" method="GET">
        <div class="search-field">
            <label>🔍 Tìm kiếm</label>
            <input type="text" name="search" class="search-input" placeholder="Tên tour, điểm đến...">
        </div>
        <div class="search-field">
            <label>🗂 Danh mục</label>
            <select name="category" class="search-select">
                <option value="">Tất cả danh mục</option>
                <?php while($c = $catsSearch->fetch_assoc()): ?>
                    <option value="<?= $c['id'] ?>"><?= $c['icon'] ?> <?= htmlspecialchars($c['name']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="search-field">
            <label>💰 Mức giá</label>
            <select name="price_max" class="search-select">
                <option value="">Tất cả mức giá</option>
                <option value="2000000">Dưới 2 triệu</option>
                <option value="4000000">Dưới 4 triệu</option>
                <option value="8000000">Dưới 8 triệu</option>
                <option value="99999999">Trên 8 triệu</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary search-btn">Tìm Tour</button>
    </form>
</section>

<!-- FEATURED TOURS -->
<section class="tours-section">
    <div class="container">
        <div class="section-header">
            <div>
                <p class="section-label">✨ Nổi Bật</p>
                <h2 class="section-title">Tour <span>Được Yêu Thích</span></h2>
            </div>
            <a href="tours.php" class="btn btn-outline btn-sm">Xem tất cả →</a>
        </div>

        <div class="tours-grid">
        <?php while ($tour = $featured->fetch_assoc()):
            $pct = $tour['max_capacity'] > 0 ? round($tour['current_bookings']/$tour['max_capacity']*100) : 0;
            $left = $tour['max_capacity'] - $tour['current_bookings'];
            $img  = $tour['image'] ? BASE_URL . '/uploads/'.$tour['image'] : 'https://picsum.photos/seed/'.$tour['id'].'/640/400';
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
                        <span class="stars"><?= $tour['avg_rating'] > 0 ? str_repeat('★', round($tour['avg_rating'])).str_repeat('☆', 5-round($tour['avg_rating'])) : '☆☆☆☆☆' ?></span>
                        <span style="color:var(--mid);font-size:.82rem">(<?= $tour['review_count'] ?>)</span>
                    </div>
                    <a href="tour-detail.php?id=<?= $tour['id'] ?>" class="btn btn-primary btn-sm">Xem Chi Tiết</a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- CATEGORIES -->
<section style="padding:60px 0;background:#fff;">
    <div class="container">
        <div class="text-center" style="margin-bottom:40px;">
            <p class="section-label">📂 Danh Mục</p>
            <h2 class="section-title">Khám Phá Theo <span>Chủ Đề</span></h2>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:16px;">
        <?php $cats->data_seek(0); while ($c = $cats->fetch_assoc()): ?>
            <a href="tours.php?category=<?= $c['id'] ?>" style="background:var(--light);border-radius:var(--radius);padding:28px 16px;text-align:center;transition:.25s ease;display:block;">
                <div style="font-size:2.4rem;margin-bottom:10px;"><?= $c['icon'] ?></div>
                <div style="font-weight:600;color:var(--dark);font-size:.95rem;"><?= htmlspecialchars($c['name']) ?></div>
            </a>
        <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- WHY CHOOSE US -->
<section style="padding:80px 0;">
    <div class="container">
        <div class="text-center" style="margin-bottom:48px;">
            <p class="section-label">💎 Tại Sao Chọn Chúng Tôi</p>
            <h2 class="section-title">Cam Kết <span>Chất Lượng</span></h2>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:28px;">
            <?php
            $features = [
                ['🛡', 'An Toàn & Uy Tín', 'Hơn 10 năm kinh nghiệm, được kiểm định chất lượng.'],
                ['💰', 'Giá Tốt Nhất', 'Cam kết giá tốt, hoàn tiền nếu tìm được nơi rẻ hơn.'],
                ['🎯', 'Tour Đa Dạng', 'Từ biển đảo, núi rừng đến tour nước ngoài.'],
                ['📞', 'Hỗ Trợ 24/7', 'Đội ngũ hỗ trợ nhiệt tình, giải đáp mọi thắc mắc.'],
            ];
            foreach ($features as $f): ?>
            <div style="background:#fff;border-radius:var(--radius);padding:28px;box-shadow:var(--shadow);text-align:center;">
                <div style="font-size:2.4rem;margin-bottom:14px;"><?= $f[0] ?></div>
                <h4 style="margin-bottom:8px;"><?= $f[1] ?></h4>
                <p style="font-size:.9rem;margin:0;"><?= $f[2] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>

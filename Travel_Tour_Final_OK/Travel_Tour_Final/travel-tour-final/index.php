<?php
include 'includes/db-connect.php';
$featured_result = mysqli_query($conn, "SELECT t.*, c.name as cat_name FROM tours t JOIN categories c ON t.category_id = c.id WHERE t.status='active' ORDER BY t.views DESC LIMIT 3");
$cat_result      = mysqli_query($conn, "SELECT c.*, COUNT(t.id) as tour_count FROM categories c LEFT JOIN tours t ON c.id = t.category_id AND t.status='active' GROUP BY c.id ORDER BY tour_count DESC");
$latest_result   = mysqli_query($conn, "SELECT t.*, c.name as cat_name FROM tours t JOIN categories c ON t.category_id = c.id WHERE t.status='active' ORDER BY t.created_at DESC LIMIT 6");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VietNam Travel – Luxury Travel Experiences</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .hero {
            height: 100vh;
            min-height: 700px;
            background: url('https://media.thuonghieucongluan.vn/uploads/2024/11/25/du-lich-viet-nam-1732534409.jpg') center/cover no-repeat;
            position: relative;
            display: flex;
            align-items: center;
        }

        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to right, rgba(13, 13, 13, 0.88) 40%, rgba(13, 13, 13, 0.3));
        }

        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 640px;
        }

        .hero-label {
            font-size: 0.75rem;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: var(--gold);
            font-weight: 700;
            margin-bottom: 20px;
            display: block;
        }

        .hero h1 {
            font-size: clamp(2.5rem, 5vw, 4.5rem);
            color: var(--white);
            line-height: 1.1;
            margin-bottom: 24px;
        }

        .hero h1 em {
            color: var(--gold);
            font-style: italic;
        }

        .hero p {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.75);
            margin-bottom: 40px;
            max-width: 480px;
            line-height: 1.8;
        }

        .hero-buttons {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        .search-wrap {
            margin-top: -60px;
            position: relative;
            z-index: 10;
        }

        .search-box {
            background: var(--white);
            border-radius: var(--radius-lg);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            padding: 36px 40px;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr auto;
            gap: 20px;
            align-items: end;
        }

        .search-box .form-group {
            margin: 0;
        }

        @media(max-width:900px) {
            .search-box {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media(max-width:580px) {
            .search-box {
                grid-template-columns: 1fr;
                padding: 24px;
            }

            .search-wrap {
                margin-top: 0;
            }
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .cat-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            padding: 36px 28px;
            text-align: center;
            border: 1px solid var(--light-2);
            transition: var(--transition);
            display: block;
        }

        .cat-card:hover {
            border-color: var(--gold);
            box-shadow: var(--shadow-gold);
            transform: translateY(-4px);
            color: inherit;
        }

        .cat-icon {
            font-size: 2.5rem;
            margin-bottom: 16px;
            display: block;
        }

        .cat-card h3 {
            font-size: 1.1rem;
            color: var(--dark);
            margin-bottom: 8px;
        }

        .cat-card p {
            font-size: 0.82rem;
            color: var(--text-light);
            margin: 0 0 12px;
        }

        .cat-count {
            font-size: 0.78rem;
            color: var(--gold);
            font-weight: 700;
            letter-spacing: 1px;
        }

        @media(max-width:768px) {
            .categories-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media(max-width:480px) {
            .categories-grid {
                grid-template-columns: 1fr;
            }
        }

        .cta-section {
            background: url('https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=1600') center/cover no-repeat;
            position: relative;
            padding: 120px 0;
            text-align: center;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(13, 13, 13, 0.75);
        }

        .cta-content {
            position: relative;
            z-index: 1;
            max-width: 640px;
            margin: 0 auto;
        }

        .cta-content h2 {
            color: var(--white);
            margin-bottom: 16px;
        }

        .cta-content p {
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 36px;
            font-size: 1.05rem;
        }
    </style>
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <span class="hero-label">Award-Winning Travel Experiences</span>
                <h1>Discover the Beauty of <em>Vietnam</em></h1>
                <p>Luxury journeys to the most extraordinary destinations across Vietnam. Expert guides, private experiences, and memories that last a lifetime.</p>
                <div class="hero-buttons">
                    <a href="tours.php" class="btn btn-gold">Explore Tours</a>
                    <a href="#featured-tours" class="btn btn-outline">View Featured</a>
                </div>
            </div>
        </div>
    </section>

    <div class="container search-wrap">
        <div class="search-box">
            <div class="form-group">
                <label>Search Tours</label>
                <input type="text" id="searchInput" class="form-control" placeholder="e.g. Ha Long, Sapa...">
            </div>
            <div class="form-group">
                <label>Category</label>
                <select id="categoryFilter" class="form-control">
                    <option value="">All Categories</option>
                    <?php
                    $cats = mysqli_query($conn, "SELECT id, name FROM categories ORDER BY name");
                    while ($cat = mysqli_fetch_assoc($cats)):
                    ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Duration</label>
                <select id="durationFilter" class="form-control">
                    <option value="">Any Duration</option>
                    <option value="1-5">1–5 Days</option>
                    <option value="6-10">6–10 Days</option>
                    <option value="11+">11+ Days</option>
                </select>
            </div>
            <button class="btn btn-gold" onclick="doSearch()">Search</button>
        </div>
    </div>


    <section class="section" id="featured-tours">
        <div class="container">
            <div class="section-header">
                <span class="section-label">Handpicked For You</span>
                <h2>Featured Experiences</h2>
                <p>Our most beloved journeys, chosen for their extraordinary beauty and unforgettable experiences.</p>
                <div class="gold-line"></div>
            </div>
            <div class="tours-grid">
                <?php while ($tour = mysqli_fetch_assoc($featured_result)): ?>
                    <div class="tour-card">
                        <div class="tour-card-image">
                            <img src="<?php echo img_url($tour['image_url']); ?>" alt="<?php echo htmlspecialchars($tour['title']); ?>">
                            <span class="tour-card-badge"><?php echo htmlspecialchars($tour['cat_name']); ?></span>
                        </div>
                        <div class="tour-card-body">
                            <div class="tour-card-destination">📍 <?php echo htmlspecialchars($tour['destination']); ?></div>
                            <h3 class="tour-card-title"><?php echo htmlspecialchars($tour['title']); ?></h3>
                            <p class="tour-card-excerpt"><?php echo substr(htmlspecialchars($tour['description']), 0, 110) . '...'; ?></p>
                            <div class="tour-card-meta">
                                <div class="tour-meta-item"><span class="icon">🕐</span><?php echo $tour['duration']; ?> Days</div>
                                <div class="tour-meta-item"><span class="icon">👥</span>Max <?php echo $tour['max_people']; ?></div>
                                <div class="tour-meta-item"><span class="icon">👁</span><?php echo number_format($tour['views']); ?> views</div>
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
            <div style="text-align:center;margin-top:50px;">
                <a href="tours.php" class="btn btn-outline">View All Tours</a>
            </div>
        </div>
    </section>

    <section class="section" style="background:var(--light-2);" id="categories">
        <div class="container">
            <div class="section-header">
                <span class="section-label">Explore By Type</span>
                <h2>Travel Categories</h2>
                <p>Find the perfect experience that matches your style of adventure.</p>
                <div class="gold-line"></div>
            </div>
            <div class="categories-grid">
                <?php mysqli_data_seek($cat_result, 0);
                while ($cat = mysqli_fetch_assoc($cat_result)): ?>
                    <a href="category.php?slug=<?php echo $cat['slug']; ?>" class="cat-card">
                        <span class="cat-icon"><?php echo $cat['icon']; ?></span>
                        <h3><?php echo htmlspecialchars($cat['name']); ?></h3>
                        <p><?php echo htmlspecialchars(substr($cat['description'], 0, 70)); ?>...</p>
                        <span class="cat-count"><?php echo $cat['tour_count']; ?> Tour<?php echo $cat['tour_count'] != 1 ? 's' : ''; ?></span>
                    </a>
                <?php endwhile; ?>
            </div>
        </div>
    </section>


    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <span class="section-label">Start Your Journey</span>
                <h2>Ready for an Unforgettable Adventure?</h2>
                <p>Browse our curated collection of luxury tours and book your dream experience today.</p>
                <a href="tours.php" class="btn btn-gold">Explore All Tours</a>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>

</html>
<?php mysqli_close($conn); ?>
<?php
require_once 'includes/db-connect.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: tours.php'); exit; }

// Fetch tour
$stmt = $conn->prepare("
    SELECT t.*, c.name AS cat_name
    FROM tours t JOIN categories c ON c.id = t.category_id
    WHERE t.id = ?
");
$stmt->bind_param('i', $id);
$stmt->execute();
$tour = $stmt->get_result()->fetch_assoc();
if (!$tour) { header('Location: tours.php'); exit; }

// Fetch reviews
$reviewsRes = $conn->query("
    SELECT r.*, u.full_name FROM reviews r
    JOIN users u ON u.id = r.user_id
    WHERE r.tour_id = $id ORDER BY r.created_at DESC
");
$avgRating = $conn->query("SELECT AVG(rating) FROM reviews WHERE tour_id=$id")->fetch_row()[0] ?? 0;
$reviewCount = $conn->query("SELECT COUNT(*) FROM reviews WHERE tour_id=$id")->fetch_row()[0] ?? 0;

$pageTitle = htmlspecialchars($tour['name']);
$img  = $tour['image'] ? BASE_URL . '/uploads/'.$tour['image'] : 'https://picsum.photos/seed/'.$id.'detail/1200/500';
$left = $tour['max_capacity'] - $tour['current_bookings'];

require_once 'includes/header.php';
?>

<!-- Tour Hero Image -->
<div class="tour-hero">
    <img src="<?= $img ?>" alt="<?= htmlspecialchars($tour['name']) ?>">
    <div class="tour-hero-overlay"></div>
    <div class="tour-hero-content">
        <span class="badge badge-coral"><?= htmlspecialchars($tour['cat_name']) ?></span>
        <h1 style="margin-top:12px;"><?= htmlspecialchars($tour['name']) ?></h1>
        <div class="tour-hero-meta">
            <span>📍 <?= htmlspecialchars($tour['destination']) ?></span>
            <span>📅 <?= $tour['duration_days'] ?> ngày</span>
            <span>👥 Tối đa <?= $tour['max_capacity'] ?> người</span>
            <?php if($reviewCount>0): ?>
                <span>⭐ <?= number_format($avgRating,1) ?> (<?= $reviewCount ?> đánh giá)</span>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="container">
    <div class="detail-layout">

        <!-- LEFT: Detail Info -->
        <div class="detail-main">

            <!-- Description -->
            <div class="detail-card">
                <h3>📝 Mô Tả Tour</h3>
                <p><?= nl2br(htmlspecialchars($tour['description'])) ?></p>
            </div>

            <!-- Highlights -->
            <?php if ($tour['highlights']): ?>
            <div class="detail-card">
                <h3>✨ Điểm Nổi Bật</h3>
                <div class="highlights-list">
                    <?php foreach(explode('|', $tour['highlights']) as $h): ?>
                        <span class="highlight-tag">✔ <?= htmlspecialchars(trim($h)) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Itinerary -->
            <?php if ($tour['itinerary']): ?>
            <div class="detail-card">
                <h3>🗓 Lịch Trình Chi Tiết</h3>
                <?php $days = explode("\n", trim($tour['itinerary'])); $n=1; ?>
                <?php foreach($days as $day): if(!trim($day)) continue; ?>
                <div class="itinerary-item">
                    <div class="itinerary-day">N<?= $n++ ?></div>
                    <div style="padding-top:8px;"><?= htmlspecialchars(trim($day)) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Reviews -->
            <div class="detail-card">
                <h3>⭐ Đánh Giá (<?= $reviewCount ?>)</h3>
                <?php if ($reviewCount > 0): ?>
                    <div style="display:flex;align-items:center;gap:16px;margin-bottom:24px;padding:16px;background:var(--light);border-radius:var(--radius);">
                        <div style="font-family:var(--font-head);font-size:3rem;color:var(--coral);"><?= number_format($avgRating,1) ?></div>
                        <div>
                            <div class="stars" style="font-size:1.4rem;"><?= str_repeat('★',round($avgRating)).str_repeat('☆',5-round($avgRating)) ?></div>
                            <div style="font-size:.85rem;color:var(--mid);"><?= $reviewCount ?> đánh giá</div>
                        </div>
                    </div>
                    <?php while($rv = $reviewsRes->fetch_assoc()): ?>
                    <div class="review-item">
                        <div class="review-header">
                            <span class="reviewer-name">👤 <?= htmlspecialchars($rv['full_name']) ?></span>
                            <span class="review-date"><?= date('d/m/Y', strtotime($rv['created_at'])) ?></span>
                        </div>
                        <div class="stars" style="font-size:1rem;margin-bottom:6px;"><?= str_repeat('★',$rv['rating']).str_repeat('☆',5-$rv['rating']) ?></div>
                        <p style="margin:0;"><?= htmlspecialchars($rv['comment']) ?></p>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="text-align:center;padding:20px;color:var(--mid);">Chưa có đánh giá. Hãy là người đầu tiên!</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- RIGHT: Booking Card -->
        <div class="detail-sidebar">
            <div class="booking-card">
                <h3>📋 Đặt Tour Ngay</h3>
                <div class="price-display">
                    <?= formatPrice($tour['price']) ?>
                    <small>/ người</small>
                </div>

                <?php if(isset($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                <?php endif; ?>
                <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php endif; ?>

                <?php if ($left <= 0): ?>
                    <div class="alert" style="background:rgba(220,53,69,.15);color:#F88;border:1px solid rgba(220,53,69,.3);">
                        ❌ Tour này đã hết chỗ
                    </div>
                <?php elseif (!isLoggedIn()): ?>
                    <div style="text-align:center;padding:16px 0;">
                        <p style="color:rgba(255,255,255,.7);margin-bottom:16px;">Đăng nhập để đặt tour</p>
                        <a href="login.php?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="btn btn-primary" style="width:100%;justify-content:center;">🔐 Đăng Nhập</a>
                        <a href="register.php" class="btn btn-outline" style="width:100%;justify-content:center;margin-top:10px;color:#fff;border-color:rgba(255,255,255,.3);">📝 Đăng Ký Tài Khoản</a>
                    </div>
                <?php else: ?>
                    <form action="booking.php" method="POST" id="bookingForm" onsubmit="return validateForm('bookingForm')">
                        <input type="hidden" name="tour_id" value="<?= $tour['id'] ?>">
                        <input type="hidden" id="price_per_person" value="<?= $tour['price'] ?>">

                        <div class="form-group">
                            <label>Số người</label>
                            <input type="number" name="num_people" id="num_people" class="form-control"
                                   min="1" max="<?= $left ?>" value="1" required>
                            <div class="field-error"></div>
                        </div>
                        <div class="form-group">
                            <label>Tên liên hệ</label>
                            <input type="text" name="contact_name" class="form-control"
                                   value="<?= htmlspecialchars($_SESSION['user_name']) ?>" required>
                            <div class="field-error"></div>
                        </div>
                        <div class="form-group">
                            <label>Số điện thoại</label>
                            <input type="tel" name="contact_phone" class="form-control"
                                   placeholder="0901234567" required>
                            <div class="field-error"></div>
                        </div>
                        <div class="form-group">
                            <label>Yêu cầu đặc biệt</label>
                            <textarea name="special_requests" class="form-control" rows="3"
                                      placeholder="Phòng riêng, chế độ ăn, hỗ trợ visa..."></textarea>
                        </div>
                        <div class="total-display">
                            <span class="total-label">Tổng tiền:</span>
                            <span class="total-amount" id="total_display"><?= formatPrice($tour['price']) ?></span>
                        </div>
                        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">✈ Xác Nhận Đặt Tour</button>
                    </form>
                <?php endif; ?>

                <div style="margin-top:20px;padding-top:20px;border-top:1px solid rgba(255,255,255,.1);">
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        <div style="font-size:.85rem;color:rgba(255,255,255,.6);">✅ Chính sách hoàn hủy linh hoạt</div>
                        <div style="font-size:.85rem;color:rgba(255,255,255,.6);">🔒 Thanh toán an toàn</div>
                        <div style="font-size:.85rem;color:rgba(255,255,255,.6);">📞 Hỗ trợ 24/7: 1800 1234</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

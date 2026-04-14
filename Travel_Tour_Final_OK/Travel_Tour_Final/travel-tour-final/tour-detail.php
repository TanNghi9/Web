<?php
include 'includes/db-connect.php';
$tour_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$result  = mysqli_query($conn, "SELECT t.*, c.name as cat_name, c.slug as cat_slug FROM tours t JOIN categories c ON t.category_id = c.id WHERE t.id = $tour_id AND t.status='active'");
if (mysqli_num_rows($result) != 1) {
    header("Location: tours.php");
    exit;
}
$tour = mysqli_fetch_assoc($result);
mysqli_query($conn, "UPDATE tours SET views = views + 1 WHERE id = $tour_id");
$related_result = mysqli_query($conn, "SELECT t.*, c.name as cat_name FROM tours t JOIN categories c ON t.category_id = c.id WHERE t.category_id = {$tour['category_id']} AND t.id != $tour_id AND t.status='active' ORDER BY t.views DESC LIMIT 3");
$booking_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name   = trim(mysqli_real_escape_string($conn, $_POST['full_name']));
    $email       = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $phone       = trim(mysqli_real_escape_string($conn, $_POST['phone']));
    $num_people  = (int)$_POST['num_people'];
    $travel_date = mysqli_real_escape_string($conn, $_POST['travel_date']);
    $message     = trim(mysqli_real_escape_string($conn, $_POST['message'] ?? ''));
    if (empty($full_name) || empty($email) || empty($phone) || empty($travel_date)) {
        $booking_error = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $booking_error = "Please enter a valid email address.";
    } elseif ($num_people < 1 || $num_people > $tour['max_people']) {
        $booking_error = "Number of people must be between 1 and {$tour['max_people']}.";
    } elseif (strtotime($travel_date) < strtotime('+1 day')) {
        $booking_error = "Travel date must be at least 1 day from today.";
    } else {
        $total_price = $tour['price'] * $num_people;
        $sql = "INSERT INTO bookings (tour_id,full_name,email,phone,num_people,travel_date,message,total_price) VALUES ($tour_id,'$full_name','$email','$phone',$num_people,'$travel_date','$message',$total_price)";
        if (mysqli_query($conn, $sql)) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                echo json_encode(['success' => true, 'booking_id' => mysqli_insert_id($conn), 'name' => $full_name]);
                exit;
            }
            header("Location: booking-success.php?id=" . mysqli_insert_id($conn));
            exit;
        } else {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                echo json_encode(['success' => false, 'message' => 'Booking failed. Please try again.']);
                exit;
            }
            $booking_error = "Booking failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($tour['title']); ?> – VietNam Travel</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .tour-detail-layout {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 48px;
            align-items: start;
        }

        .tour-hero-image {
            width: 100%;
            height: 520px;
            object-fit: cover;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            margin-bottom: 40px;
        }

        .tour-info-bar {
            display: flex;
            background: var(--white);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 40px;
        }

        .info-bar-item {
            flex: 1;
            padding: 24px 20px;
            text-align: center;
            border-right: 1px solid var(--light-2);
        }

        .info-bar-item:last-child {
            border-right: none;
        }

        .info-bar-icon {
            font-size: 1.5rem;
            margin-bottom: 8px;
            display: block;
        }

        .info-bar-label {
            font-size: 0.72rem;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--gray);
            font-weight: 700;
            display: block;
            margin-bottom: 4px;
        }

        .info-bar-value {
            font-family: var(--font-display);
            font-size: 1.1rem;
            color: var(--dark);
            font-weight: 600;
        }

        .tour-content h2 {
            font-size: 1.6rem;
            margin: 36px 0 16px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--light-2);
        }

        .tour-full-content {
            line-height: 1.9;
            font-size: 0.95rem;
            color: var(--text-light);
        }

        .booking-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            overflow: hidden;
            position: sticky;
            top: 100px;
        }

        .booking-card-header {
            background: var(--dark);
            padding: 28px 30px;
            text-align: center;
        }

        .booking-card-header .price-label {
            font-size: 0.75rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.5);
            margin-bottom: 8px;
        }

        .booking-card-header .price {
            font-family: var(--font-display);
            font-size: 2.2rem;
            color: var(--gold);
            display: block;
        }

        .booking-card-header .price-per {
            font-size: 0.82rem;
            color: rgba(255, 255, 255, 0.5);
        }

        .booking-form {
            padding: 30px;
        }

        .booking-form h3 {
            font-size: 1.1rem;
            margin-bottom: 24px;
            padding-bottom: 14px;
            border-bottom: 1px solid var(--light-2);
        }

        .price-summary {
            background: var(--light);
            border-radius: var(--radius);
            padding: 16px;
            margin: 20px 0;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.88rem;
            margin-bottom: 8px;
            color: var(--text-light);
        }

        .price-row.total {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid var(--light-2);
            font-weight: 700;
            color: var(--dark);
            font-size: 1rem;
        }

        .price-row.total .amount {
            color: var(--gold-dark);
            font-family: var(--font-display);
        }

        .related-section {
            margin-top: 70px;
            padding-top: 50px;
            border-top: 2px solid var(--light-2);
        }

        @media(max-width:960px) {
            .tour-detail-layout {
                grid-template-columns: 1fr;
            }

            .booking-card {
                position: static;
            }
        }

        @media(max-width:640px) {
            .tour-info-bar {
                flex-wrap: wrap;
            }

            .info-bar-item {
                flex: 1 1 50%;
                border-bottom: 1px solid var(--light-2);
            }
        }
    </style>
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="page-hero">
        <div class="container">
            <span class="section-label"><a href="category.php?slug=<?php echo $tour['cat_slug']; ?>" style="color:var(--gold)"><?php echo htmlspecialchars($tour['cat_name']); ?></a></span>
            <h1><?php echo htmlspecialchars($tour['title']); ?></h1>
            <p>📍 <?php echo htmlspecialchars($tour['destination']); ?></p>
        </div>
    </div>

    <section class="section" style="padding-top:50px;">
        <div class="container">
            <img src="<?php echo img_url($tour['image_url']); ?>" alt="<?php echo htmlspecialchars($tour['title']); ?>" class="tour-hero-image">
            <div class="tour-info-bar">
                <div class="info-bar-item"><span class="info-bar-icon">🕐</span><span class="info-bar-label">Duration</span><span class="info-bar-value"><?php echo $tour['duration']; ?> Days</span></div>
                <div class="info-bar-item"><span class="info-bar-icon">👥</span><span class="info-bar-label">Group Size</span><span class="info-bar-value">Max <?php echo $tour['max_people']; ?></span></div>
                <div class="info-bar-item"><span class="info-bar-icon">📍</span><span class="info-bar-label">Destination</span><span class="info-bar-value"><?php echo htmlspecialchars($tour['destination']); ?></span></div>
                <div class="info-bar-item"><span class="info-bar-icon">👁</span><span class="info-bar-label">Views</span><span class="info-bar-value"><?php echo number_format($tour['views']); ?></span></div>
                <div class="info-bar-item"><span class="info-bar-icon">💰</span><span class="info-bar-label">Price</span><span class="info-bar-value"><?php echo number_format($tour['price'], 0, ',', '.'); ?> ₫</span></div>
            </div>
            <div class="tour-detail-layout">
                <div class="tour-content">
                    <h2>Overview</h2>
                    <p><?php echo htmlspecialchars($tour['description']); ?></p>
                    <?php if (!empty($tour['content'])): ?>
                        <h2>Tour Itinerary</h2>
                        <div class="tour-full-content"><?php echo nl2br(htmlspecialchars($tour['content'])); ?></div>
                    <?php endif; ?>
                    <div style="margin-top:36px;display:flex;gap:12px;flex-wrap:wrap;">
                        <a href="tours.php" class="btn btn-outline">← Back to Tours</a>
                        <a href="#booking" class="btn btn-gold">Book This Tour</a>
                    </div>
                </div>
                <div id="booking">
                    <?php if (!empty($booking_error)): ?><div class="alert alert-error" style="margin-bottom:20px;"><?php echo htmlspecialchars($booking_error); ?></div><?php endif; ?>
                    <div class="booking-card">
                        <div class="booking-card-header">
                            <p class="price-label">Starting From</p>
                            <span class="price"><?php echo number_format($tour['price'], 0, ',', '.'); ?> ₫</span>
                            <span class="price-per">per person</span>
                        </div>
                        <div class="booking-form">
                            <h3>📋 Book This Tour</h3>
                            <form method="POST" action="tour-detail.php?id=<?php echo $tour_id; ?>#booking" id="bookingForm">
                                <div class="form-group">
                                    <label>Full Name <span class="req">*</span></label>
                                    <input type="text" name="full_name" class="form-control" placeholder="Your full name" required value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                                </div>
                                <div class="form-group">
                                    <label>Email Address <span class="req">*</span></label>
                                    <input type="email" name="email" class="form-control" placeholder="your@email.com" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                </div>
                                <div class="form-group">
                                    <label>Phone Number <span class="req">*</span></label>
                                    <input type="tel" name="phone" class="form-control" placeholder="+84 xxx xxx xxx" required value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                                </div>
                                <div class="form-group">
                                    <label>Travel Date <span class="req">*</span></label>
                                    <input type="date" name="travel_date" class="form-control" required id="travelDate"
                                        min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>"
                                        value="<?php echo isset($_POST['travel_date']) ? htmlspecialchars($_POST['travel_date']) : date('Y-m-d', strtotime('-1 day')); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Number of People <span class="req">*</span></label>
                                    <input type="number" name="num_people" class="form-control" id="numPeople" min="1" max="<?php echo $tour['max_people']; ?>" required value="<?php echo isset($_POST['num_people']) ? (int)$_POST['num_people'] : 1; ?>">
                                    <span class="form-hint">Max <?php echo $tour['max_people']; ?> people</span>
                                </div>
                                <div class="price-summary">
                                    <div class="price-row"><span>Price per person</span><span><?php echo number_format($tour['price'], 0, ',', '.'); ?> ₫</span></div>
                                    <div class="price-row"><span>Travelers</span><span id="displayPeople">1</span></div>
                                    <div class="price-row total"><span>Total</span><span class="amount" id="totalPrice"><?php echo number_format($tour['price'], 0, ',', '.'); ?> ₫</span></div>
                                </div>
                                <div class="form-group">
                                    <label>Special Requests</label>
                                    <textarea name="message" class="form-control" rows="3" placeholder="Any special requirements..."><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                                </div>
                                <button type="submit" class="btn btn-gold" style="width:100%;">✓ Confirm Booking</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (mysqli_num_rows($related_result) > 0): ?>
                <div class="related-section">
                    <h2>More <?php echo htmlspecialchars($tour['cat_name']); ?> Tours</h2>
                    <div class="tours-grid">
                        <?php while ($rel = mysqli_fetch_assoc($related_result)): ?>
                            <div class="tour-card">
                                <div class="tour-card-image">
                                    <img src="<?php echo img_url($rel['image_url']); ?>" alt="<?php echo htmlspecialchars($rel['title']); ?>" loading="lazy">
                                    <span class="tour-card-badge"><?php echo htmlspecialchars($rel['cat_name']); ?></span>
                                </div>
                                <div class="tour-card-body">
                                    <div class="tour-card-destination">📍 <?php echo htmlspecialchars($rel['destination']); ?></div>
                                    <h3 class="tour-card-title"><?php echo htmlspecialchars($rel['title']); ?></h3>
                                    <p class="tour-card-excerpt"><?php echo substr(htmlspecialchars($rel['description']), 0, 100) . '...'; ?></p>
                                    <div class="tour-card-meta">
                                        <div class="tour-meta-item"><span class="icon">🕐</span><?php echo $rel['duration']; ?> Days</div>
                                        <div class="tour-meta-item"><span class="icon">👥</span>Max <?php echo $rel['max_people']; ?></div>
                                    </div>
                                    <div class="tour-card-footer">
                                        <div class="tour-price">
                                            <span class="from">From</span>
                                            <span class="amount"><?php echo number_format($rel['price'], 0, ',', '.'); ?> ₫</span>
                                            <span class="per">/ person</span>
                                        </div>
                                        <a href="tour-detail.php?id=<?php echo $rel['id']; ?>" class="btn btn-gold btn-sm">View</a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <!-- ── Booking Success Modal ── -->
    <div id="bookingModal" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;">
        <div id="modalBackdrop" style="position:absolute;inset:0;background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);"></div>
        <div style="position:relative;background:var(--white);border-radius:var(--radius-lg);box-shadow:0 24px 60px rgba(0,0,0,0.25);max-width:480px;width:90%;padding:48px 40px;text-align:center;animation:modalIn .35s ease;">
            <div style="width:72px;height:72px;background:linear-gradient(135deg,var(--gold),var(--gold-light));border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;font-size:2rem;">✓</div>
            <h2 style="font-family:var(--font-display);font-size:1.8rem;color:var(--dark);margin-bottom:10px;">Booking Confirmed!</h2>
            <p id="modalGreeting" style="color:var(--text-light);font-size:1rem;margin-bottom:6px;"></p>
            <p id="modalBookingId" style="display:inline-block;background:var(--light);color:var(--gold-dark);font-weight:700;font-size:0.9rem;padding:6px 18px;border-radius:20px;border:1px solid var(--gold);margin-bottom:24px;letter-spacing:1px;"></p>
            <p style="color:var(--text-light);font-size:0.88rem;margin-bottom:32px;">Our team will contact you shortly to confirm the details.</p>
            <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
                <a href="tours.php" class="btn btn-outline" style="min-width:150px;">Browse More Tours</a>
                <a href="index.php" class="btn btn-gold" style="min-width:150px;">Return to Home</a>
            </div>
            <button onclick="closeModal()" style="position:absolute;top:16px;right:20px;background:none;border:none;font-size:1.5rem;cursor:pointer;color:var(--gray);line-height:1;">&times;</button>
        </div>
    </div>

    <style>
        @keyframes modalIn {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.96);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
    </style>

    <script>
        const pricePerPerson = <?php echo $tour['price']; ?>;
        const numPeopleInput = document.getElementById('numPeople');

        function updatePrice() {
            const n = parseInt(numPeopleInput.value) || 1;
            document.getElementById('displayPeople').textContent = n;
            document.getElementById('totalPrice').textContent = (pricePerPerson * n).toLocaleString('vi-VN') + ' ₫';
        }
        numPeopleInput.addEventListener('input', updatePrice);

        function closeModal() {
            document.getElementById('bookingModal').style.display = 'none';
        }
        document.getElementById('modalBackdrop').addEventListener('click', closeModal);
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeModal();
        });

        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const name = this.full_name.value.trim();
            const email = this.email.value.trim();
            const phone = this.phone.value.trim();
            const date = this.travel_date.value;
            const n = parseInt(this.num_people.value);

            if (!name || !email || !phone || !date) {
                alert('Please fill in all required fields.');
                return;
            }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                alert('Invalid email address.');
                return;
            }
            if (n < 1 || n > <?php echo $tour['max_people']; ?>) {
                alert('Invalid number of people.');
                return;
            }

            const submitBtn = this.querySelector('button[type="submit"]');
            const origText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '⏳ Processing...';

            const formData = new FormData(this);
            fetch(this.action || window.location.href, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('modalGreeting').textContent = 'Thank you, ' + data.name + '. Your adventure awaits!';
                        document.getElementById('modalBookingId').textContent = 'Booking #' + String(data.booking_id).padStart(6, '0');
                        const modal = document.getElementById('bookingModal');
                        modal.style.display = 'flex';
                        document.getElementById('bookingForm').reset();
                        updatePrice();
                    } else {
                        alert(data.message || 'Booking failed. Please try again.');
                    }
                })
                .catch(() => alert('An error occurred. Please try again.'))
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = origText;
                });
        });
    </script>
</body>

</html>
<?php mysqli_close($conn); ?>
<?php
include 'includes/db-connect.php';
$booking_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$result = mysqli_query($conn, "SELECT b.*, t.title as tour_title, t.duration, t.destination, t.image_url FROM bookings b JOIN tours t ON b.tour_id = t.id WHERE b.id = $booking_id");
if (mysqli_num_rows($result) != 1) { header("Location: index.php"); exit; }
$booking = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed – VietNam Travel</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .success-page { min-height:100vh; background:var(--light); padding:120px 0 80px; display:flex; align-items:center; }
        .success-card { max-width:680px; margin:0 auto; background:var(--white); border-radius:var(--radius-lg); box-shadow:0 20px 60px rgba(0,0,0,0.1); overflow:hidden; }
        .success-top { background:var(--dark); padding:50px 40px; text-align:center; }
        .success-icon { width:80px; height:80px; background:rgba(201,168,76,0.15); border:2px solid var(--gold); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:2rem; margin:0 auto 20px; }
        .success-top h1 { color:var(--white); font-size:1.8rem; margin-bottom:10px; }
        .success-top p { color:rgba(255,255,255,0.6); font-size:0.95rem; margin:0; }
        .booking-ref { display:inline-block; background:var(--gold); color:var(--dark); padding:8px 20px; border-radius:20px; font-weight:700; font-size:0.85rem; letter-spacing:1px; margin-top:16px; }
        .success-body { padding:40px; }
        .tour-summary { display:flex; gap:20px; padding:20px; background:var(--light); border-radius:var(--radius); margin-bottom:32px; align-items:center; }
        .tour-summary img { width:100px; height:70px; object-fit:cover; border-radius:var(--radius); flex-shrink:0; }
        .tour-summary h3 { font-size:1.05rem; margin-bottom:4px; color:var(--dark); }
        .tour-summary p { font-size:0.85rem; color:var(--text-light); margin:0; }
        .booking-details { border:1px solid var(--light-2); border-radius:var(--radius); overflow:hidden; margin-bottom:32px; }
        .detail-row { display:flex; padding:14px 20px; border-bottom:1px solid var(--light-2); font-size:0.9rem; }
        .detail-row:last-child { border-bottom:none; }
        .detail-label { font-weight:700; color:var(--text); min-width:160px; font-size:0.82rem; }
        .detail-value { color:var(--text-light); }
        .detail-value.highlight { color:var(--gold-dark); font-weight:700; font-family:var(--font-display); font-size:1rem; }
        .success-actions { display:flex; gap:12px; flex-wrap:wrap; }
        .success-actions .btn { flex:1; min-width:160px; text-align:center; }
    </style>
</head>
<body>
<nav class="navbar scrolled">
    <div class="nav-container">
        <a href="index.php" class="nav-logo">VietNam <span>Travel</span></a>
        <ul class="nav-menu">
            <li><a href="index.php">Home</a></li>
            <li><a href="tours.php">All Tours</a></li>
        </ul>
    </div>
</nav>
<div class="success-page">
    <div class="container">
        <div class="success-card">
            <div class="success-top">
                <div class="success-icon">✓</div>
                <h1>Booking Confirmed!</h1>
                <p>Thank you, <?php echo htmlspecialchars($booking['full_name']); ?>. Your adventure awaits!</p>
                <span class="booking-ref">Booking #<?php echo str_pad($booking_id, 6, '0', STR_PAD_LEFT); ?></span>
            </div>
            <div class="success-body">
                <div class="tour-summary">
                    <img src="<?php echo htmlspecialchars($booking['image_url']); ?>" alt="<?php echo htmlspecialchars($booking['tour_title']); ?>">
                    <div>
                        <h3><?php echo htmlspecialchars($booking['tour_title']); ?></h3>
                        <p>📍 <?php echo htmlspecialchars($booking['destination']); ?> &nbsp;|&nbsp; 🕐 <?php echo $booking['duration']; ?> Days</p>
                    </div>
                </div>
                <div class="booking-details">
                    <div class="detail-row"><span class="detail-label">Traveler Name</span><span class="detail-value"><?php echo htmlspecialchars($booking['full_name']); ?></span></div>
                    <div class="detail-row"><span class="detail-label">Email</span><span class="detail-value"><?php echo htmlspecialchars($booking['email']); ?></span></div>
                    <div class="detail-row"><span class="detail-label">Phone</span><span class="detail-value"><?php echo htmlspecialchars($booking['phone']); ?></span></div>
                    <div class="detail-row"><span class="detail-label">Travel Date</span><span class="detail-value"><?php echo date('F j, Y', strtotime($booking['travel_date'])); ?></span></div>
                    <div class="detail-row"><span class="detail-label">Travelers</span><span class="detail-value"><?php echo $booking['num_people']; ?> person<?php echo $booking['num_people']>1?'s':''; ?></span></div>
                    <div class="detail-row"><span class="detail-label">Booking Date</span><span class="detail-value"><?php echo date('F j, Y', strtotime($booking['created_at'])); ?></span></div>
                    <div class="detail-row"><span class="detail-label">Total Amount</span><span class="detail-value highlight"><?php echo number_format($booking['total_price'], 0, ',', '.'); ?> ₫</span></div>
                    <div class="detail-row"><span class="detail-label">Status</span><span class="detail-value" style="color:var(--success);font-weight:600;">● <?php echo ucfirst($booking['status']); ?></span></div>
                    <?php if (!empty($booking['message'])): ?>
                    <div class="detail-row"><span class="detail-label">Special Requests</span><span class="detail-value"><?php echo htmlspecialchars($booking['message']); ?></span></div>
                    <?php endif; ?>
                </div>
                <p style="font-size:0.88rem;color:var(--text-light);margin-bottom:28px;text-align:center;">
                    Our team will contact you at <strong><?php echo htmlspecialchars($booking['email']); ?></strong> shortly.
                </p>
                <div class="success-actions">
                    <a href="tours.php" class="btn btn-outline">Browse More Tours</a>
                    <a href="index.php" class="btn btn-gold">Return to Home</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="js/main.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>

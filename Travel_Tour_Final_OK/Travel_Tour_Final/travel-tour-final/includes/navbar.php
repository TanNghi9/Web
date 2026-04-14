<?php
// ============================================================
// includes/navbar.php – Shared Navigation
// ============================================================
$current = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar" id="navbar">
    <div class="nav-container">
        <a href="index.php" class="nav-logo">VietNam <span>Travel</span></a>
        <ul class="nav-menu">
            <li><a href="index.php"        <?php echo $current==='index.php'   ?'style="color:var(--gold)"':''; ?>>Home</a></li>
            <li><a href="tours.php"        <?php echo $current==='tours.php'   ?'style="color:var(--gold)"':''; ?>>All Tours</a></li>
            <li><a href="index.php#categories">Destinations</a></li>
            <li><a href="index.php#why-us">About</a></li>
            <li><a href="tours.php" class="nav-cta">Book Now</a></li>
        </ul>
    </div>
</nav>

<?php
// includes/footer.php – Shared Footer
$footer_cats = mysqli_query($conn, "SELECT name, slug FROM categories ORDER BY name LIMIT 6");
?>
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <a href="index.php" class="nav-logo">VietNam <span>Travel</span></a>
                <p>Crafting extraordinary travel experiences across Vietnam since 2009.</p>
            </div>
            <div>
                <h4 class="footer-heading">Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="tours.php">All Tours</a></li>
                    <li><a href="index.php#why-us">About Us</a></li>
                    <li><a href="admin-tours.php">Admin Panel</a></li>
                </ul>
            </div>
            <div>
                <h4 class="footer-heading">Categories</h4>
                <ul class="footer-links">
                    <?php while ($fc = mysqli_fetch_assoc($footer_cats)): ?>
                    <li><a href="category.php?slug=<?php echo $fc['slug']; ?>"><?php echo htmlspecialchars($fc['name']); ?></a></li>
                    <?php endwhile; ?>
                </ul>
            </div>
            <div>
                <h4 class="footer-heading">Contact</h4>
                <ul class="footer-links">
                    <li><a href="#">✉ info@vietnamtravel.vn</a></li>
                    <li><a href="#">📞 +84 923 472 321</a></li>
                    <li><a href="#">📍 Ho Chi Minh City, Vietnam</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 <span>VietNam Travel</span>. All rights reserved.</p>
        </div>
    </div>
</footer>

<button id="backToTop" title="Back to top">↑</button>
<script src="js/main.js"></script>

<footer class="footer">
    <div class="footer-content">
        <div class="footer-col">
            <div class="footer-logo">✈ TravelVN</div>
            <p>Khám phá vẻ đẹp Việt Nam và thế giới cùng chúng tôi.</p>
        </div>
        <div class="footer-col">
            <h4>Liên Kết Nhanh</h4>
            <ul>
                <li><a href="<?= BASE_URL ?>/index.php">Trang Chủ</a></li>
                <li><a href="<?= BASE_URL ?>/tours.php">Tất Cả Tour</a></li>
                <li><a href="<?= BASE_URL ?>/tours.php?category=1">Tour Biển Đảo</a></li>
                <li><a href="<?= BASE_URL ?>/tours.php?category=2">Tour Núi Rừng</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Liên Hệ</h4>
            <ul class="contact-list">
                <li>📍 123 Nguyễn Văn Linh, Cần Thơ</li>
                <li>📞 1800 1234 (Miễn phí)</li>
                <li>✉ info@travelvn.com</li>
                <li>🕐 8:00 – 21:00 (T2 – CN)</li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <p>© 2025 TravelVN – CT214H Web Programming Final Project</p>
    </div>
</footer>

<button id="backToTop" title="Về đầu trang">↑</button>

<script src="<?= BASE_URL ?>/js/main.js"></script>
<?= isset($extraJS) ? $extraJS : '' ?>
</body>
</html>

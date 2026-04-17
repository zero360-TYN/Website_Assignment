<footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <img src="/img/cematrix.png" alt="Logo" class="footer-logo">
                <p class="footer-about">Every unboxing is an unknown adventure. We bring the trendiest blind boxes to your doorstep.</p>
            </div>

            <div class="footer-section">
                <h4 class="footer-title">Shop</h4>
                <ul class="footer-links">
                    <li><a href="/index.php">Home</a></li>
                    <li><a href="/product/shop.php">All Products</a></li>
                    <li><a href="/user/voucher.php">Vouchers</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4 class="footer-title">Support</h4>
                <ul class="footer-links">
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Shipping Policy</a></li>
                    <li><a href="#">Contact Us</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4 class="footer-title">Follow Us</h4>
                <div class="social-icons">
                    <a href="#"><i class='bx bxl-facebook-square'></i></a>
                    <a href="#"><i class='bx bxl-instagram'></i></a>
                    <a href="#"><i class='bx bxl-tiktok'></i></a>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2026 CEMATRIX. All Rights Reserved.</p>
        </div>
    </footer>

    <script src="/JS/base.js"></script>
    <?php if(isset($_jsFileName) && !empty($_jsFileName)):?>
        <script src="/JS/<?= $_jsFileName ?>.js"></script>
    <?php endif ?>
</body>
</html>
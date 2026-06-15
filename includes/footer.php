</main>

<footer class="footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <h6 class="text-uppercase fw-semibold small mb-3"><?= SITE_NAME ?></h6>
                <p class="small text-muted" style="max-width: 40ch;">Curated luxury fashion for the discerning wardrobe. Every piece selected for quality, craftsmanship, and enduring style.</p>
            </div>
            <div class="col-6 col-lg-2">
                <h6 class="text-uppercase fw-semibold small mb-3">Shop</h6>
                <ul class="list-unstyled">
                    <li class="mb-1"><a href="shop.php">All Products</a></li>
                    <li class="mb-1"><a href="shop.php?category=1">Ready-to-Wear</a></li>
                    <li class="mb-1"><a href="shop.php?category=2">Outerwear</a></li>
                    <li class="mb-1"><a href="shop.php?category=3">Footwear</a></li>
                    <li class="mb-1"><a href="shop.php?category=4">Accessories</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <h6 class="text-uppercase fw-semibold small mb-3">Account</h6>
                <ul class="list-unstyled">
                    <?php if (isLoggedIn()): ?>
                        <li class="mb-1"><a href="account.php">My Account</a></li>
                        <li class="mb-1"><a href="account.php?action=orders">Order History</a></li>
                        <li class="mb-1"><a href="account.php?action=logout">Sign Out</a></li>
                    <?php else: ?>
                        <li class="mb-1"><a href="account.php?action=login">Sign In</a></li>
                        <li class="mb-1"><a href="account.php?action=register">Create Account</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <h6 class="text-uppercase fw-semibold small mb-3">Support</h6>
                <ul class="list-unstyled">
                    <li class="mb-1"><a href="#">Shipping & Returns</a></li>
                    <li class="mb-1"><a href="#">Size Guide</a></li>
                    <li class="mb-1"><a href="#">Care Instructions</a></li>
                    <li class="mb-1"><a href="#">Contact</a></li>
                </ul>
            </div>
        </div>
        <div class="row mt-4 pt-3 border-top">
            <div class="col text-center">
                <p class="text-muted small mb-0">&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js" defer></script>
</body>
</html>

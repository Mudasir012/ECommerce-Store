<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$featuredMen = getFeaturedProducts($pdo, 4, 'men');
$featuredWomen = getFeaturedProducts($pdo, 4, 'women');
$featuredKids = getFeaturedProducts($pdo, 4, 'kids');
$categories = getCategories($pdo);

$pageTitle = 'Home';
$pageDesc = 'Curated luxury fashion for the discerning wardrobe.';

// Enable transparent nav on homepage
$transparentNav = true;
include 'includes/header.php';
?>

<section class="hero" id="hero">
    <img class="hero__bg" src="https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=1600&q=85" alt="Luxury fashion editorial" loading="eager">
    <div class="hero__overlay"></div>
    <div class="container">
        <div class="hero__content">
            <p class="text-uppercase ls-wider small fw-medium mb-3" style="letter-spacing: 0.12em;">The Spring Collection</p>
            <h1 class="hero__title">Refined by nature.<br><strong>Built to last.</strong></h1>
            <p class="fs-5 fw-light mb-4 opacity-85" style="max-width: 45ch; line-height: 1.6;">A curated edit of essentials for the entire family — from Italian-spun merino to vegetable-tanned leather. Every piece selected for quality and enduring style.</p>
            <div class="d-flex gap-3 flex-wrap">
                <a href="shop.php" class="btn btn-gold btn-lg px-5 fw-semibold text-uppercase small ls-wider">Explore the Collection</a>
                <a href="shop.php?gender=men" class="btn btn-outline-white btn-lg px-4 fw-medium">Men</a>
                <a href="shop.php?gender=women" class="btn btn-outline-white btn-lg px-4 fw-medium">Women</a>
                <a href="shop.php?gender=kids" class="btn btn-outline-white btn-lg px-4 fw-medium">Kids</a>
            </div>
        </div>
    </div>
</section>

<!-- Men's Featured -->
<section class="py-5" style="padding-top: 5rem !important;">
    <div class="container">
        <div class="row align-items-end mb-5">
            <div class="col-md-8">
                <p class="text-uppercase ls-wider small fw-medium text-muted mb-2 reveal">Men's Edit</p>
                <h2 class="fw-light display-5 mb-2 reveal reveal-delay-1">For Him</h2>
                <p class="text-muted fs-5 fw-light mb-0 reveal reveal-delay-2">Tailored essentials for the modern wardrobe</p>
            </div>
            <div class="col-md-4 text-md-end reveal reveal-delay-3">
                <a href="shop.php?gender=men" class="btn btn-outline-primary rounded-0 px-4 d-none d-md-inline-flex">All Men's →</a>
            </div>
        </div>
        <div class="row g-4">
            <?php foreach ($featuredMen as $i => $product): ?>
                <div class="col-6 col-md-3 reveal" style="transition-delay: <?= 0.1 + $i * 0.1 ?>s">
                    <a href="product.php?slug=<?= htmlspecialchars($product['slug']) ?>" class="product-card">
                        <div class="product-card__image-wrap position-relative">
                            <img class="product-card__image" src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['image_alt'] ?: $product['name']) ?>" loading="lazy">
                            <?php if ($product['compare_price']): ?>
                                <span class="product-card__badge badge position-absolute top-0 start-0 mt-2 ms-2" style="background: var(--brand-gold);">Sale</span>
                            <?php endif; ?>
                            <div class="product-card__quick-add text-center">
                                <span class="btn btn-sm btn-light rounded-0 px-4 fw-medium small text-uppercase ls-wider">Quick View</span>
                            </div>
                        </div>
                        <div class="pt-3">
                            <p class="small text-muted text-uppercase ls-wider mb-1" style="font-size: 0.6875rem;"><?= htmlspecialchars($product['category_name'] ?? '') ?></p>
                            <h3 class="fs-6 fw-medium mb-1"><?= htmlspecialchars($product['name']) ?></h3>
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-medium"><?= formatPrice($product['price']) ?></span>
                                <?php if ($product['compare_price']): ?>
                                    <span class="text-muted text-decoration-line-through small"><?= formatPrice($product['compare_price']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4 d-md-none reveal">
            <a href="shop.php?gender=men" class="btn btn-outline-primary rounded-0 px-5">All Men's →</a>
        </div>
    </div>
</section>

<!-- Women's Featured -->
<section class="py-5" style="background: var(--brand-surface);">
    <div class="container">
        <div class="row align-items-end mb-5">
            <div class="col-md-8">
                <p class="text-uppercase ls-wider small fw-medium text-muted mb-2 reveal">Women's Edit</p>
                <h2 class="fw-light display-5 mb-2 reveal reveal-delay-1">For Her</h2>
                <p class="text-muted fs-5 fw-light mb-0 reveal reveal-delay-2">Curated pieces from silk blouses to tailored coats</p>
            </div>
            <div class="col-md-4 text-md-end reveal reveal-delay-3">
                <a href="shop.php?gender=women" class="btn btn-outline-primary rounded-0 px-4 d-none d-md-inline-flex">All Women's →</a>
            </div>
        </div>
        <div class="row g-4">
            <?php foreach ($featuredWomen as $i => $product): ?>
                <div class="col-6 col-md-3 reveal" style="transition-delay: <?= 0.1 + $i * 0.1 ?>s">
                    <a href="product.php?slug=<?= htmlspecialchars($product['slug']) ?>" class="product-card">
                        <div class="product-card__image-wrap position-relative">
                            <img class="product-card__image" src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['image_alt'] ?: $product['name']) ?>" loading="lazy">
                            <?php if ($product['compare_price']): ?>
                                <span class="product-card__badge badge position-absolute top-0 start-0 mt-2 ms-2" style="background: var(--brand-gold);">Sale</span>
                            <?php endif; ?>
                            <div class="product-card__quick-add text-center">
                                <span class="btn btn-sm btn-light rounded-0 px-4 fw-medium small text-uppercase ls-wider">Quick View</span>
                            </div>
                        </div>
                        <div class="pt-3">
                            <p class="small text-muted text-uppercase ls-wider mb-1" style="font-size: 0.6875rem;"><?= htmlspecialchars($product['category_name'] ?? '') ?></p>
                            <h3 class="fs-6 fw-medium mb-1"><?= htmlspecialchars($product['name']) ?></h3>
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-medium"><?= formatPrice($product['price']) ?></span>
                                <?php if ($product['compare_price']): ?>
                                    <span class="text-muted text-decoration-line-through small"><?= formatPrice($product['compare_price']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4 d-md-none reveal">
            <a href="shop.php?gender=women" class="btn btn-outline-primary rounded-0 px-5">All Women's →</a>
        </div>
    </div>
</section>

<!-- Kids' Featured -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-end mb-5">
            <div class="col-md-8">
                <p class="text-uppercase ls-wider small fw-medium text-muted mb-2 reveal">Kids' Edit</p>
                <h2 class="fw-light display-5 mb-2 reveal reveal-delay-1">For Little Ones</h2>
                <p class="text-muted fs-5 fw-light mb-0 reveal reveal-delay-2">Playful, durable designs made to last</p>
            </div>
            <div class="col-md-4 text-md-end reveal reveal-delay-3">
                <a href="shop.php?gender=kids" class="btn btn-outline-primary rounded-0 px-4 d-none d-md-inline-flex">All Kids' →</a>
            </div>
        </div>
        <div class="row g-4">
            <?php foreach ($featuredKids as $i => $product): ?>
                <div class="col-6 col-md-3 reveal" style="transition-delay: <?= 0.1 + $i * 0.1 ?>s">
                    <a href="product.php?slug=<?= htmlspecialchars($product['slug']) ?>" class="product-card">
                        <div class="product-card__image-wrap position-relative">
                            <img class="product-card__image" src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['image_alt'] ?: $product['name']) ?>" loading="lazy">
                            <?php if ($product['compare_price']): ?>
                                <span class="product-card__badge badge position-absolute top-0 start-0 mt-2 ms-2" style="background: var(--brand-gold);">Sale</span>
                            <?php endif; ?>
                            <div class="product-card__quick-add text-center">
                                <span class="btn btn-sm btn-light rounded-0 px-4 fw-medium small text-uppercase ls-wider">Quick View</span>
                            </div>
                        </div>
                        <div class="pt-3">
                            <p class="small text-muted text-uppercase ls-wider mb-1" style="font-size: 0.6875rem;"><?= htmlspecialchars($product['category_name'] ?? '') ?></p>
                            <h3 class="fs-6 fw-medium mb-1"><?= htmlspecialchars($product['name']) ?></h3>
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-medium"><?= formatPrice($product['price']) ?></span>
                                <?php if ($product['compare_price']): ?>
                                    <span class="text-muted text-decoration-line-through small"><?= formatPrice($product['compare_price']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4 d-md-none reveal">
            <a href="shop.php?gender=kids" class="btn btn-outline-primary rounded-0 px-5">All Kids' →</a>
        </div>
    </div>
</section>

<!-- Shop by Category -->
<section class="py-5" style="background: var(--brand-surface);">
    <div class="container">
        <div class="row align-items-end mb-5">
            <div class="col-md-8">
                <p class="text-uppercase ls-wider small fw-medium text-muted mb-2 reveal">Collections</p>
                <h2 class="fw-light display-5 mb-2 reveal reveal-delay-1">Shop by Category</h2>
                <p class="text-muted fs-5 fw-light mb-0 reveal reveal-delay-2">Curated edits across every wardrobe essential</p>
            </div>
        </div>
        <div class="collection-grid reveal">
            <?php
            $categoryImages = [
                1 => 'https://images.unsplash.com/photo-1560343090-f0409e92791a?w=800&q=80',
                2 => 'https://images.unsplash.com/photo-1544022613-e87ca75a784a?w=800&q=80',
                3 => 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=800&q=80',
                4 => 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?w=800&q=80',
            ];
            foreach ($categories as $cat):
            ?>
                <a href="shop.php?category=<?= $cat['id'] ?>" class="collection-card">
                    <img class="collection-card__image" src="<?= $categoryImages[$cat['id']] ?? '' ?>" alt="<?= htmlspecialchars($cat['name']) ?>" loading="lazy">
                    <div class="collection-card__overlay"></div>
                    <div class="collection-card__content">
                        <h3 class="h3 fw-light mb-1"><?= htmlspecialchars($cat['name']) ?></h3>
                        <span class="small text-uppercase ls-wider fw-medium opacity-75">Explore →</span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="py-5 text-center">
    <div class="container" style="max-width: 600px;">
        <div class="mb-4 reveal">
            <span class="display-1 fw-light text-muted" style="line-height: 0.8;">✦</span>
        </div>
        <h2 class="fw-light h3 mb-3 reveal reveal-delay-1">Free shipping on orders over $500</h2>
        <p class="text-muted mb-0 reveal reveal-delay-2">Complimentary shipping and returns on all domestic orders over $500. International shipping available.</p>
    </div>
</section>

<section class="py-5" style="background: var(--brand-green); color: #fff;">
    <div class="container text-center" style="max-width: 700px;">
        <p class="text-uppercase ls-wider small fw-medium mb-3 opacity-75 reveal">The LUXE Standard</p>
        <h2 class="fw-light display-6 mb-4 reveal reveal-delay-1">Quality you can feel. <br class="d-none d-md-inline">Craftsmanship you can trust.</h2>
        <p class="opacity-75 mb-0 reveal reveal-delay-2" style="max-width: 55ch; margin: 0 auto;">Every piece in our collection is personally selected for its materials, construction, and enduring design. We work directly with mills, tanneries, and artisans who share our commitment to doing things right.</p>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

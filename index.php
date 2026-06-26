<?php
require_once 'config/config.php';
$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 6");
$featured_products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/style.css">

    <title>Fatima Store - Home</title>
</head>

<body>

    <?php include 'includes/header.php'; ?>

    <section class="hero-section">
        <div class="hero-container">

            <div class="hero-content">
                <h1>The Future of Tech <br> is <span class="highlight">Here</span></h1>
                <p>Experience precision-engineered electronics designed for the professional edge. From high-performance computing to intelligent lifestyle gadgets.</p>

                <div class="hero-buttons">
                    <a href="products.php" class="btn btn-primary">Shop Now</a>
                    <a href="contact.php" class="btn btn-outline">Get in Touch</a>
                </div>
            </div>

            <div class="hero-image">
                <img src="images/laptop.png" alt="laptop">
            </div>

        </div>
    </section>

    <section class="featured-products">
        <div class="section-container">

            <div class="section-header">
                <div class="title-wrapper">
                    <h2>Featured Electronics</h2>
                    <div class="title-line"></div>
                </div>
                <a href="products.php" class="view-all-btn">View All Collections <i class="fa-solid fa-arrow-right"></i></a>
            </div>

            <div class="products-grid">
                <?php if (count($featured_products) > 0): ?>
                    <?php foreach ($featured_products as $p): ?>
                        <div class="product-card">
                            <div class="product-image-container">
                                <?php if ($p['stock_quantity'] < 5 && $p['stock_quantity'] > 0): ?>
                                    <span class="badge badge-sale">LOW STOCK</span>
                                <?php elseif ($p['stock_quantity'] == 0): ?>
                                    <span class="badge" style="background:#dc3545;">OUT OF STOCK</span>
                                <?php endif; ?>
                                <a href="product_detail.php?id=<?= $p['product_id'] ?>">
                                    <img src="images/<?= htmlspecialchars($p['image_url']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                                </a>
                            </div>
                            <div class="product-info">
                                <h3><a href="product_detail.php?id=<?= $p['product_id'] ?>" style="color:inherit; text-decoration:none;"><?= htmlspecialchars($p['name']) ?></a></h3>
                                <p class="product-desc"><?= htmlspecialchars(substr($p['description'], 0, 50)) ?>...</p>
                                <div class="product-footer">
                                    <span class="product-price">$<?= number_format($p['price'], 2) ?></span>
                                    <form action="add_to_cart.php" method="POST" style="margin:0;">
                                        <input type="hidden" name="product_id" value="<?= $p['product_id'] ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="add-to-cart-btn" style="border:none; cursor:pointer; color:inherit;" <?= $p['stock_quantity'] <= 0 ? 'disabled' : '' ?>>
                                            <i class="fa-solid fa-cart-shopping"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No featured products found.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

</body>

</html>
<?php
require_once 'config/config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->execute([$_GET['id']]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: products.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fatima Store - <?= htmlspecialchars($product['name']) ?></title>
    <meta name="description" content="<?= htmlspecialchars(substr($product['description'], 0, 160)) ?>">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/productsdetails.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php include 'includes/header.php'; ?>
    <div class="product-detail-wrapper">
        <div class="product-gallery">
            <div class="main-image-container">
                <img src="images/<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            </div>
        </div>

        <div class="product-info-container">
            <h1 class="product-title"><?= htmlspecialchars($product['name']) ?></h1>
            <?php
            ?>
            <div class="product-price">$<?= htmlspecialchars(number_format($product['price'], 2)) ?></div>
            <p class="product-description">
                <?= nl2br(htmlspecialchars($product['description'])) ?>
            </p>

            <form action="add_to_cart.php" method="POST">
                <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                <div class="product-purchase-row">
                    <div class="quantity-selector">
                        <button type="button" class="qty-btn" onclick="let input = document.getElementById('qty-input'); if(input.value > 1) input.value--;"><i class="fa-solid fa-minus"></i></button>
                        <input type="number" name="quantity" id="qty-input" class="qty-input" value="1" min="1" max="<?= $product['stock_quantity'] > 0 ? $product['stock_quantity'] : 1 ?>">
                        <button type="button" class="qty-btn" onclick="document.getElementById('qty-input').value++;"><i class="fa-solid fa-plus"></i></button>
                    </div>

                    <button type="submit" class="btn-add-to-cart" <?= $product['stock_quantity'] <= 0 ? 'disabled' : '' ?>>
                        <i class="fa-solid fa-cart-shopping"></i> <?= $product['stock_quantity'] > 0 ? 'ADD TO CART' : 'OUT OF STOCK' ?>
                    </button>
                </div>
            </form>

        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

</body>

</html>
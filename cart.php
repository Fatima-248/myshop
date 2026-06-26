<?php
require_once 'config/config.php';
require_once 'includes/auth_check.php';

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT c.cart_id as cart_id, c.quantity, p.product_id as product_id, p.name, p.price, p.image_url, p.description 
                       FROM cart_items c 
                       JOIN products p ON c.product_id = p.product_id 
                       WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$shipping = 0; // Free shipping
$total = $subtotal + $shipping;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fatima Store - Shopping Cart</title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/cart.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <?php include 'includes/header.php'; ?>
    <div class="cart-page-container">

        <main class="cart-main-content">
            <h1 class="cart-title">Shopping Cart <span class="cart-count">(<?= count($cart_items) ?> Items)</span></h1>

            <?php if (count($cart_items) > 0): ?>
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th class="col-product">Product</th>
                            <th class="col-name">Name</th>
                            <th class="col-price">Unit Price</th>
                            <th class="col-qty">Quantity</th>
                            <th class="col-subtotal">Subtotal</th>
                            <th class="col-action"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item): ?>
                            <tr>
                                <td class="col-product">
                                    <div class="cart-img-wrapper">
                                        <img src="images/<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                                    </div>
                                </td>
                                <td class="col-name">
                                    <div class="cart-item-details">
                                        <h3><?= htmlspecialchars($item['name']) ?></h3>
                                        <p><?= htmlspecialchars(substr($item['description'], 0, 50)) ?>...</p>
                                    </div>
                                </td>
                                <td class="col-price">$<?= number_format($item['price'], 2) ?></td>
                                <td class="col-qty">
                                    <form action="update_cart.php" method="POST" class="quantity-control" style="display: flex; align-items: center;">
                                        <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                        <button type="submit" name="action" value="decrease" class="qty-btn">-</button>
                                        <input type="text" value="<?= $item['quantity'] ?>" readonly style="width: 40px; text-align: center;">
                                        <button type="submit" name="action" value="increase" class="qty-btn">+</button>
                                    </form>
                                </td>
                                <td class="col-subtotal font-bold">$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                                <td class="col-action">
                                    <form action="remove_from_cart.php" method="POST" style="margin: 0;">
                                        <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                        <button type="submit" class="cart-remove-btn" style="border:none; background:none; cursor:pointer;"><i class="fa-regular fa-trash-can"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Your cart is currently empty.</p>
            <?php endif; ?>

            <a href="products.php" class="back-to-shop-link">
                <i class="fa-solid fa-arrow-left"></i> Back to Shop
            </a>
        </main>

        <aside class="cart-summary-sidebar">
            <div class="summary-card">
                <h2>Order Summary</h2>
                <hr>
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span class="summary-value">$<?= number_format($subtotal, 2) ?></span>
                </div>
                <div class="summary-row">
                    <span>Shipping</span>
                    <span class="badge-free-shipping">FREE</span>
                </div>

                <hr class="summary-divider">

                <div class="summary-total-row">
                    <span>Total</span>
                    <span class="total-price">$<?= number_format($total, 2) ?></span>
                </div>

                <?php if (count($cart_items) > 0): ?>
                    <a href="checkout.php" class="btn-checkout-submit" style="display:block; text-align:center; text-decoration:none;">
                        Proceed to Checkout <i class="fa-solid fa-cart-shopping"></i>
                    </a>
                <?php endif; ?>

            </div>
        </aside>

    </div>
    <?php include 'includes/footer.php'; ?>
</body>

</html>
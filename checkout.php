<?php
require_once 'config/config.php';
require_once 'includes/auth_check.php';

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT c.quantity, p.product_id, p.name, p.price, p.stock_quantity FROM cart_items c JOIN products p ON c.product_id = p.product_id WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

if (empty($cart_items)) {
    header("Location: products.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$error = '';
$success = '';
$total_amount = 0;
foreach ($cart_items as $item) {
    $total_amount += $item['price'] * $item['quantity'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shipping_address = trim($_POST['shipping_address']);

    if (empty($shipping_address)) {
        $error = "Shipping address is required.";
    } else {
        try {
            $pdo->beginTransaction();

            // Create order
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status, shipping_address) VALUES (?, ?, 'pending', ?)");
            $stmt->execute([$user_id, $total_amount, $shipping_address]);
            $order_id = $pdo->lastInsertId();

            // Create order_items
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
            foreach ($cart_items as $item) {
                $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
                // Optional: Update stock
                $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?")->execute([$item['quantity'], $item['product_id']]);
            }

            // Clear cart
            $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?");
            $stmt->execute([$user_id]);

            // Update user address if it's their first time entering it
            if (empty($user['address'])) {
                $stmt = $pdo->prepare("UPDATE users SET address = ? WHERE user_id = ?");
                $stmt->execute([$shipping_address, $user_id]);
            }

            $pdo->commit();
            $success = "Order placed successfully! Redirecting to your profile...";
            header("refresh:3;url=profile.php");
            $cart_items = []; // Prevent displaying cart items after success
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "An error occurred while placing your order. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fatima Store - Checkout</title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .checkout-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .checkout-container h2 {
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .form-group textarea,
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .order-summary {
            background: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .order-summary h3 {
            margin-bottom: 15px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 10px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .btn-submit {
            display: block;
            width: 100%;
            padding: 12px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        .btn-submit:hover {
            background: #0056b3;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
        }
    </style>
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <div class="checkout-container">
        <h2>Checkout</h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php else: ?>

            <div class="order-summary">
                <h3>Order Summary</h3>
                <?php foreach ($cart_items as $item): ?>
                    <div class="summary-item">
                        <span><?= htmlspecialchars($item['name']) ?> (x<?= $item['quantity'] ?>)</span>
                        <span>$<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                    </div>
                <?php endforeach; ?>
                <div class="summary-item" style="font-weight: bold; font-size: 1.2em; border-top: 1px solid #e5e7eb; padding-top: 10px; margin-top: 10px;">
                    <span>Total Amount:</span>
                    <span>$<?= number_format($total_amount, 2) ?></span>
                </div>
            </div>

            <form action="checkout.php" method="POST">
                <div class="form-group">
                    <label for="shipping_address">Shipping Address</label>
                    <textarea id="shipping_address" name="shipping_address" rows="4" required><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label>Payment Method</label>
                    <p style="color: #666; font-size: 0.9em;"><i class="fa-solid fa-lock"></i> Cash on Delivery (COD) only for now.</p>
                </div>

                <button type="submit" class="btn-submit">Place Order</button>
            </form>

        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>

</html>
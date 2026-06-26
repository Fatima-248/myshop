<?php
require_once 'config/config.php';
require_once 'includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("SELECT cart_id, quantity FROM cart_items WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $cart_item = $stmt->fetch();

    if ($cart_item) {
        $new_quantity = $cart_item['quantity'] + $quantity;
        $stmt = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE cart_id = ?");
        $stmt->execute([$new_quantity, $cart_item['cart_id']]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $product_id, $quantity]);
    }
}
header("Location: cart.php");
exit();
?>

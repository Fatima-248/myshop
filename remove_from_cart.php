<?php
require_once 'config/config.php';
require_once 'includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cart_id = $_POST['cart_id'];
    $stmt = $pdo->prepare("DELETE FROM cart_items WHERE cart_id = ? AND user_id = ?");
    $stmt->execute([$cart_id, $_SESSION['user_id']]);
}
header("Location: cart.php");
exit();
?>

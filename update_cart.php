<?php
require_once 'config/config.php';
require_once 'includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cart_id = $_POST['cart_id'];
    $action = $_POST['action'];

    $stmt = $pdo->prepare("SELECT quantity FROM cart_items WHERE cart_id = ? AND user_id = ?");
    $stmt->execute([$cart_id, $_SESSION['user_id']]);
    $item = $stmt->fetch();

    if ($item) {
        $qty = $item['quantity'];
        if ($action === 'increase') $qty++;
        elseif ($action === 'decrease' && $qty > 1) $qty--;

        $stmt = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE cart_id = ?");
        $stmt->execute([$qty, $cart_id]);
    }
}
header("Location: cart.php");
exit();
?>

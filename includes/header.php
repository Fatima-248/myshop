<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header class="custom-header">
    <div class="header-container">

        <div class="header-logo">
            <a href="index.php">Fatima Store</a>
        </div>

        <?php include 'navbar.php'; ?>

        <div class="header-icons">
            <?php
            $cart_count = 0;
            if (isset($_SESSION['user_id'])) {
                require_once __DIR__ . '/../config/config.php';
                $stmt = $pdo->prepare("SELECT SUM(quantity) as count FROM cart_items WHERE user_id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $row = $stmt->fetch();
                $cart_count = $row['count'] ? $row['count'] : 0;
            }
            ?>
            <a href="cart.php" class="icon-link">
                <i class="fa-solid fa-cart-shopping"></i>
                <?php if ($cart_count > 0): ?>
                    <span style="background: red; color: white; border-radius: 50%; padding: 2px 6px; font-size: 12px; position: absolute; transform: translate(-10px, -10px);"><?php echo $cart_count; ?></span>
                <?php endif; ?>
            </a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="profile.php" class="icon-link" title="Profile"><i class="fa-regular fa-circle-user"></i></a>
                <a href="logout.php" class="icon-link" title="Logout"><i class="fa-solid fa-sign-out-alt"></i></a>
            <?php else: ?>
                <a href="login.php" class="icon-link" title="Login"><i class="fa-regular fa-circle-user"></i></a>
            <?php endif; ?>
        </div>

    </div>
</header>
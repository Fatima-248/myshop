<nav class="header-nav">
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="products.php">Shop</a></li>
        <li><a href="contact.php">Contact</a></li>
        <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin'): ?>
            <li><a href="admin/admin_dashboard.php">Admin Panel</a></li>
        <?php endif; ?>
    </ul>
</nav>

<aside class="sidebar">
    <div class="sidebar-header">
        <h2>Fatima Store Admin</h2>
        <p>System Controller</p>
    </div>

    <nav class="sidebar-nav">
        <a href="admin_dashboard.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php' ? 'active' : '' ?>">
            <div class="nav-item-content">
                <i class="fa-solid fa-border-all"></i>
                Dashboard
            </div>
        </a>
        <a href="admin_products.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'admin_products.php' ? 'active' : '' ?>">
            <div class="nav-item-content">
                <i class="fa-solid fa-box-archive"></i>
                Products
            </div>
        </a>
        <a href="admin_orders.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'admin_orders.php' ? 'active' : '' ?>">
            <div class="nav-item-content">
                <i class="fa-solid fa-bag-shopping"></i>
                Orders
            </div>
        </a>
        <a href="admin_users.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'admin_users.php' ? 'active' : '' ?>">
            <div class="nav-item-content">
                <i class="fa-solid fa-user-group"></i>
                Users
            </div>
        </a>
        <a href="admin_messages.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'admin_messages.php' ? 'active' : '' ?>">
            <div class="nav-item-content">
                <i class="fa-regular fa-envelope"></i>
                Messages
            </div>
        </a>
        <a href="../index.php" class="nav-item">
            <div class="nav-item-content">
                <i class="fa-solid fa-store"></i>
                View Store
            </div>
        </a>
        <a href="../logout.php" class="nav-item">
            <div class="nav-item-content" style="color: #ef4444;">
                <i class="fa-solid fa-sign-out-alt"></i>
                Logout
            </div>
        </a>
    </nav>

    <div class="sidebar-footer">
        <hr class="sidebar-divider" />

        <a href="admin_products.php?action=add" class="btn-sidebar-add" style="display:block; text-align:center; text-decoration:none;">
            <i class="fa-solid fa-plus"></i> Add New Product
        </a>

        <div class="admin-profile">
            <div class="avatar">
                <img src="../images/profile.png" alt="Admin" onerror="this.src='../images/profile.png'" />
            </div>
            <div class="profile-info">
                <span class="name"><?= htmlspecialchars($_SESSION['name'] ?? 'Admin') ?></span>
                <span class="role">System Manager</span>
            </div>
        </div>
    </div>
</aside>
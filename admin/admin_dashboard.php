<?php
require_once '../config/config.php';
require_once '../includes/admin_check.php';

$stmt = $pdo->query("SELECT COUNT(*) FROM products");
$total_products = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM orders");
$total_orders = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'");
$total_users = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM contacts WHERE is_read = 0");
$unread_messages = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT o.*, u.full_name as customer_name FROM orders o JOIN users u ON o.user_id = u.user_id ORDER BY o.order_date DESC LIMIT 5");
$recent_orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>admin_dashboard - Fatima Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin-orders.css">
    <link rel="stylesheet" href="../css/admin-dashboard.css">
</head>

<body>

    <div
        class="app-layout"
        style="display: flex; flex-direction: row; min-height: 100vh">

        <?php include '../includes/sidebar.php'; ?>

        <div class="main-content-wrapper">
            <header class="page-top-header">
                <div class="orders-title">
                    <button
                        type="button"
                        class="sidebar-toggle-btn"
                        aria-label="Open sidebar">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <div>
                        <h1>Welcome, <?= htmlspecialchars($_SESSION['name'] ?? 'Admin') ?></h1>
                        <p style="color: var(--secondary-text); margin: 5px 0 0 0;"><?= date('l, F j, Y \a\t h:i A') ?></p>
                    </div>
                </div>
            </header>

            <main class="main-content">

                <div class="stats-grid">

                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div class="icon-wrapper bg-blue">
                                <i class="fa-solid fa-box"></i>
                            </div>
                        </div>
                        <div class="stat-card-body">
                            <h3>TOTAL PRODUCTS</h3>
                            <h2><?= $total_products ?></h2>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div class="icon-wrapper bg-orange">
                                <i class="fa-solid fa-bag-shopping"></i>
                            </div>
                        </div>
                        <div class="stat-card-body">
                            <h3>TOTAL ORDERS</h3>
                            <h2><?= $total_orders ?></h2>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div class="icon-wrapper bg-green">
                                <i class="fa-solid fa-user-group"></i>
                            </div>
                        </div>
                        <div class="stat-card-body">
                            <h3>TOTAL USERS</h3>
                            <h2><?= $total_users ?></h2>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div class="icon-wrapper bg-red">
                                <i class="fa-regular fa-envelope"></i>
                            </div>
                            <?php if ($unread_messages > 0): ?>
                                <span class="badge-urgent">Urgent</span>
                            <?php endif; ?>
                        </div>
                        <div class="stat-card-body">
                            <h3>UNREAD MESSAGES</h3>
                            <h2><?= $unread_messages ?></h2>
                        </div>
                    </div>

                </div>

                <div class="recent-orders-container">
                    <div class="section-header">
                        <h2>Recent Orders</h2>
                        <a href="admin_orders.php" class="view-all">View All Orders</a>
                    </div>

                    <div class="orders-table-scroll">
                        <table class="dashboard-orders-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_orders as $order): ?>
                                    <tr>
                                        <td class="font-bold">#ORD-<?= str_pad($order['order_id'], 5, '0', STR_PAD_LEFT) ?></td>
                                        <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                        <td><?= date('M d, Y', strtotime($order['order_date'])) ?></td>
                                        <td>$<?= number_format($order['total_amount'], 2) ?></td>
                                        <td><span class="status-badge <?= strtolower($order['status']) ?>"><?= ucfirst($order['status']) ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($recent_orders)): ?>
                                    <tr>
                                        <td colspan="5" style="text-align: center;">No recent orders.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <div class="sidebar-overlay"></div>

    <script src="../js/script.js"></script>

</body>

</html>
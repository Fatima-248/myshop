<?php
require_once '../config/config.php';
require_once '../includes/admin_check.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
    $stmt->execute([$status, $order_id]);
    header("Location: admin_orders.php");
    exit();
}

$stmt = $pdo->query("SELECT o.*, u.full_name as customer_name FROM orders o JOIN users u ON o.user_id = u.user_id ORDER BY o.order_date DESC");
$orders = $stmt->fetchAll();

$view_order = null;
if (isset($_GET['order_id'])) {
    $stmt = $pdo->prepare("SELECT o.*, u.full_name as customer_name, u.email as customer_email, u.phone as customer_phone FROM orders o JOIN users u ON o.user_id = u.user_id WHERE o.order_id = ?");
    $stmt->execute([$_GET['order_id']]);
    $view_order = $stmt->fetch();
    if ($view_order) {
        $stmt = $pdo->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.product_id WHERE oi.order_id = ?");
        $stmt->execute([$view_order['order_id']]);
        $view_order['items'] = $stmt->fetchAll();
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="../css/sidebar.css" />
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/admin-orders.css" />
    <title>Manage Orders - Fatima store</title>
</head>

<body>
    <div class="app-layout" style="display: flex; flex-direction: row; min-height: 100vh">
        <?php include '../includes/sidebar.php'; ?>

        <div class="main-content-wrapper">
            <header class="page-top-header">
                <div class="orders-title">
                    <button type="button" class="sidebar-toggle-btn" aria-label="Open sidebar">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <h1>Manage Orders</h1>
                </div>
            </header>

            <main class="main-content">
                <?php if ($view_order): ?>
                    <div class="orders-container" style="padding: 20px; background: white; border-radius: 8px;">
                        <h2>Order Details #<?= $view_order['order_id'] ?></h2>
                        <a href="admin_orders.php" style="color: #007bff; text-decoration: none; margin-bottom: 20px; display: inline-block;">&larr; Back to Orders</a>

                        <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                            <div>
                                <p><strong>Customer:</strong> <?= htmlspecialchars($view_order['customer_name']) ?></p>
                                <p><strong>Email:</strong> <?= htmlspecialchars($view_order['customer_email']) ?></p>
                                <p><strong>Phone:</strong> <?= htmlspecialchars($view_order['customer_phone'] ?? 'N/A') ?></p>
                                <p><strong>Date:</strong> <?= date('M d, Y', strtotime($view_order['order_date'])) ?></p>
                            </div>
                            <div>
                                <p><strong>Shipping Address:</strong><br><?= nl2br(htmlspecialchars($view_order['shipping_address'])) ?></p>
                            </div>
                        </div>

                        <table class="orders-table">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($view_order['items'] as $item): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['name']) ?></td>
                                        <td><?= $item['quantity'] ?></td>
                                        <td>$<?= number_format($item['unit_price'], 2) ?></td>
                                        <td>$<?= number_format($item['unit_price'] * $item['quantity'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <h3 style="text-align: right; margin-top: 20px;">Total: $<?= number_format($view_order['total_amount'], 2) ?></h3>
                    </div>
                <?php else: ?>
                    <div class="orders-container">
                        <div class="orders-table-wrapper">
                            <table class="orders-table">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer Name</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td class="order-id">#TN-<?= str_pad($order['order_id'], 5, '0', STR_PAD_LEFT) ?></td>
                                            <td>
                                                <div class="customer-profile-cell">
                                                    <div class="avatar-circle avatar-blue"><?= strtoupper(substr($order['customer_name'], 0, 2)) ?></div>
                                                    <span class="customer-name"><?= htmlspecialchars($order['customer_name']) ?></span>
                                                </div>
                                            </td>
                                            <td class="order-date"><?= date('M d, Y', strtotime($order['order_date'])) ?></td>
                                            <td class="order-total">$<?= number_format($order['total_amount'], 2) ?></td>
                                            <td>
                                                <form action="admin_orders.php" method="POST" style="margin: 0;">
                                                    <input type="hidden" name="update_status" value="1">
                                                    <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                                    <div class="status-select-wrapper dark-theme">
                                                        <select name="status" class="status-select" onchange="this.form.submit()">
                                                            <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                                            <option value="paid" <?= $order['status'] === 'paid' ? 'selected' : '' ?>>Paid</option>
                                                            <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                                            <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                                            <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                                        </select>
                                                    </div>
                                                </form>
                                            </td>
                                            <td class="text-right">
                                                <div class="actions-cell">
                                                    <a href="admin_orders.php?order_id=<?= $order['order_id'] ?>" class="view-details-link">View Details</a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <div class="sidebar-overlay"></div>
    <script src="../js/script.js"></script>
</body>

</html>
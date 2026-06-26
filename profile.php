<?php
require_once 'config/config.php';
require_once 'includes/auth_check.php';

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, address = ? WHERE user_id = ?");
    if ($stmt->execute([$name, $email, $phone, $address, $user_id])) {
        $success = "Profile updated successfully!";
        $_SESSION['name'] = $name;
    } else {
        $error = "Failed to update profile.";
    }
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();

$view_order = null;
if (isset($_GET['order_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
    $stmt->execute([$_GET['order_id'], $user_id]);
    $view_order = $stmt->fetch();
    if ($view_order) {
        $stmt = $pdo->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.product_id WHERE oi.order_id = ?");
        $stmt->execute([$view_order['order_id']]);
        $view_order['items'] = $stmt->fetchAll();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/profile.css">
    <link rel="stylesheet" href="css/style.css">
    <title>Fatima Store - Profile</title>
</head>

<body>
    <?php include 'includes/header.php'; ?>
    <div class="dashboard-wrapper">
        <aside class="dashboard-sidebar">
            <div class="user-profile-summary">
                <div class="avatar-wrapper">
                    <img src="images/profile.png" alt="<?= htmlspecialchars($user['full_name']) ?>">
                </div>
                <div class="user-meta">
                    <h3><?= htmlspecialchars($user['full_name']) ?></h3>
                    <p>Member since <?= date('Y', strtotime($user['created_at'])) ?></p>
                </div>
            </div>

            <nav class="sidebar-nav">
                <a href="profile.php" class="nav-item <?= !isset($_GET['edit']) && !isset($_GET['order_id']) ? 'active' : '' ?>">
                    <i class="fa-regular fa-user"></i> Profile
                </a>
                <a href="profile.php?edit=1" class="nav-item <?= isset($_GET['edit']) ? 'active' : '' ?>">
                    <i class="fa-solid fa-pen"></i> Edit Profile
                </a>
                <a href="logout.php" class="nav-item logout-btn">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
                </a>
            </nav>
        </aside>

        <main class="dashboard-content">

            <?php if ($success): ?>
                <div style="background: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 4px;"><?= $success ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border-radius: 4px;"><?= $error ?></div>
            <?php endif; ?>

            <?php if (isset($_GET['edit'])): ?>
                <section class="dashboard-card personal-info-card">
                    <div class="card-header">
                        <h2>Edit Personal Information</h2>
                    </div>
                    <form method="POST" action="profile.php">
                        <input type="hidden" name="update_profile" value="1">
                        <div style="margin-bottom: 15px;">
                            <label>Full Name</label>
                            <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required style="width: 100%; padding: 8px;">
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label>Email Address</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required style="width: 100%; padding: 8px;">
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label>Phone Number</label>
                            <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" style="width: 100%; padding: 8px;">
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label>Default Address</label>
                            <textarea name="address" rows="3" style="width: 100%; padding: 8px;"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                        </div>
                        <button type="submit" style="background: #007bff; color: white; border: none; padding: 10px 20px; cursor: pointer;">Save Changes</button>
                    </form>
                </section>
            <?php elseif (isset($_GET['order_id']) && $view_order): ?>
                <section class="dashboard-card order-history-card">
                    <div class="card-header">
                        <h2>Order Details #<?= $view_order['order_id'] ?></h2>
                        <a href="profile.php" style="color: #007bff; text-decoration: none;">Back to Profile</a>
                    </div>
                    <p><strong>Date:</strong> <?= date('M d, Y', strtotime($view_order['order_date'])) ?></p>
                    <p><strong>Status:</strong> <?= ucfirst($view_order['status']) ?></p>
                    <p><strong>Shipping Address:</strong> <?= htmlspecialchars($view_order['shipping_address']) ?></p>
                    <table class="orders-table" style="margin-top: 20px; width: 100%;">
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
                </section>
            <?php else: ?>
                <section class="dashboard-card personal-info-card">
                    <div class="card-header">
                        <h2>Personal Information</h2>
                        <a href="profile.php?edit=1" class="btn-edit-profile" style="text-decoration:none;"><i class="fa-solid fa-pen"></i> Edit Profile</a>
                    </div>
                    <div class="info-grid">
                        <div class="info-block">
                            <span>FULL NAME</span>
                            <p><?= htmlspecialchars($user['full_name']) ?></p>
                        </div>
                        <div class="info-block">
                            <span>EMAIL ADDRESS</span>
                            <p><?= htmlspecialchars($user['email']) ?></p>
                        </div>
                        <div class="info-block">
                            <span>PHONE NUMBER</span>
                            <p><?= htmlspecialchars($user['phone'] ?? 'N/A') ?></p>
                        </div>
                        <div class="info-block">
                            <span>DEFAULT ADDRESS</span>
                            <p><?= htmlspecialchars($user['address'] ?? 'N/A') ?></p>
                        </div>
                    </div>
                </section>

                <section class="dashboard-card order-history-card">
                    <div class="card-header">
                        <h2>Order History</h2>
                    </div>
                    <div class="table-responsive">
                        <table class="orders-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($orders) > 0): ?>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td class="order-id">#TN-<?= str_pad($order['order_id'], 5, '0', STR_PAD_LEFT) ?></td>
                                            <td><?= date('M d, Y', strtotime($order['order_date'])) ?></td>
                                            <td class="order-total">$<?= number_format($order['total_amount'], 2) ?></td>
                                            <td><span class="db-status-badge status-<?= strtolower($order['status']) ?>"><?= ucfirst($order['status']) ?></span></td>
                                            <td><a href="profile.php?order_id=<?= $order['order_id'] ?>" class="action-link">View Details <i class="fa-solid fa-chevron-right"></i></a></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" style="text-align: center;">No orders found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            <?php endif; ?>

        </main>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>

</html>
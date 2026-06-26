<?php
require_once '../config/config.php';
require_once '../includes/admin_check.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['change_role'])) {
        $user_id = $_POST['user_id'];
        $role = $_POST['role'];
        if ($user_id != $_SESSION['user_id']) {
            $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE user_id = ?");
            $stmt->execute([$role, $user_id]);
        } else {
            $error = "You cannot change your own role.";
        }
    } elseif (isset($_POST['delete_user'])) {
        $user_id = $_POST['user_id'];
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
        $stmt->execute([$user_id]);
        if ($stmt->fetchColumn() == 0) {
            if ($user_id != $_SESSION['user_id']) {
                $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
                $stmt->execute([$user_id]);
            } else {
                $error = "You cannot delete yourself.";
            }
        } else {
            $error = "Cannot delete user. User has existing orders.";
        }
    }
    if (empty($error)) {
        header("Location: admin_users.php");
        exit();
    }
}

$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin-orders.css">
    <link rel="stylesheet" href="../css/admin-users.css">
    <title>admin_user- Fatima Store</title>
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
                    <div>
                        <h1>User Management</h1>
                        <p style="color: var(--secondary-text); margin: 5px 0 0 0;">Monitor and control access for all TechNova stakeholders.</p>
                    </div>
                </div>
            </header>

            <main class="main-content">
                <div class="users-container">

                    <?php if ($error): ?>
                        <div style="background: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border-radius: 4px;"><?= $error ?></div>
                    <?php endif; ?>

                    <div class="filters-stats-row">
                        <div class="total-users-card">
                            <span class="card-label">TOTAL USERS</span>
                            <span class="card-count"><?= count($users) ?></span>
                        </div>
                    </div>

                    <div class="users-table-wrapper">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Role</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $u): ?>
                                    <tr>
                                        <td class="user-id">#TN-<?= str_pad($u['user_id'], 4, '0', STR_PAD_LEFT) ?></td>
                                        <td>
                                            <div class="user-profile-cell">
                                                <div class="avatar-circle avatar-orange"><?= strtoupper(substr($u['full_name'], 0, 2)) ?></div>
                                                <span class="user-full-name"><?= htmlspecialchars($u['full_name']) ?></span>
                                            </div>
                                        </td>
                                        <td class="user-email"><?= htmlspecialchars($u['email']) ?></td>
                                        <td class="user-phone"><?= htmlspecialchars($u['phone'] ?? 'N/A') ?></td>
                                        <td>
                                            <?php if ($u['user_id'] != $_SESSION['user_id']): ?>
                                                <form action="admin_users.php" method="POST" style="margin: 0;">
                                                    <input type="hidden" name="change_role" value="1">
                                                    <input type="hidden" name="user_id" value="<?= $u['user_id'] ?>">
                                                    <div class="role-select-wrapper">
                                                        <select class="role-select" name="role" onchange="this.form.submit()">
                                                            <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                                            <option value="customer" <?= $u['role'] === 'customer' ? 'selected' : '' ?>>Customer</option>
                                                        </select>
                                                    </div>
                                                </form>
                                            <?php else: ?>
                                                <div class="role-select-wrapper no-arrow">
                                                    <span style="padding: 6px 12px; display: inline-block; font-size: 13px; font-weight: 500; border-radius: 4px; border: 1px solid #e2e8f0; background: #f8fafc; color: #475569;">Admin (You)</span>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($u['user_id'] != $_SESSION['user_id']): ?>
                                                <form action="admin_users.php" method="POST" style="margin: 0; display: inline;">
                                                    <input type="hidden" name="delete_user" value="1">
                                                    <input type="hidden" name="user_id" value="<?= $u['user_id'] ?>">
                                                    <button class="btn-delete-user" type="submit" onclick="return confirm('Are you sure you want to delete this user?')"><i class="fa-regular fa-trash-can"></i></button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
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
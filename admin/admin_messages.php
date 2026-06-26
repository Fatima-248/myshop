<?php
require_once '../config/config.php';
require_once '../includes/admin_check.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['mark_read'])) {
        $msg_id = $_POST['msg_id'];
        $stmt = $pdo->prepare("UPDATE contacts SET is_read = 1 WHERE message_id = ?");
        $stmt->execute([$msg_id]);
    } elseif (isset($_POST['delete_msg'])) {
        $msg_id = $_POST['msg_id'];
        $stmt = $pdo->prepare("DELETE FROM contacts WHERE message_id = ?");
        $stmt->execute([$msg_id]);
    }
    header("Location: admin_messages.php");
    exit();
}

$stmt = $pdo->query("SELECT * FROM contacts ORDER BY `submitted_ at` DESC");
$messages = $stmt->fetchAll();
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
    <link rel="stylesheet" href="../css/admin-messages.css">
    <title>Manage message - Fatima Store</title>
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
                        <h1>Manage Messages</h1>
                        <p style="color: var(--secondary-text); margin: 5px 0 0 0;">Review and respond to customer inquiries and support tickets.</p>
                    </div>
                </div>
            </header>

            <main class="main-content">
                <div class="messages-container">
                    <div class="messages-table-wrapper">
                        <table class="messages-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Subject</th>
                                    <th>Message Details</th>
                                    <th>Date</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($messages as $msg): ?>
                                    <tr class="<?= $msg['is_read'] == 0 ? 'unread-message' : '' ?>">
                                        <td>
                                            <div class="sender-profile">
                                                <span class="unread-dot <?= $msg['is_read'] == 1 ? 'read' : '' ?>"></span>
                                                <div class="sender-details">
                                                    <span class="sender-name"><?= htmlspecialchars($msg['name']) ?></span>
                                                    <span class="sender-email"><?= htmlspecialchars($msg['email']) ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 500; color: var(--primary-text);"><?= htmlspecialchars($msg['subject']) ?></div>
                                        </td>
                                        <td>
                                            <div class="msg-preview" style="max-width: 300px; white-space: pre-wrap;"><?= htmlspecialchars(substr($msg['message'], 0, 80)) ?><?= strlen($msg['message']) > 80 ? '...' : '' ?></div>
                                        </td>
                                        <td class="msg-date"><?= date('M d, Y H:i', strtotime($msg['submitted_ at'])) ?></td>
                                        <td>
                                            <div class="message-actions">
                                                <?php if ($msg['is_read'] == 0): ?>
                                                    <form action="admin_messages.php" method="POST" style="margin: 0; display: inline;">
                                                        <input type="hidden" name="mark_read" value="1">
                                                        <input type="hidden" name="msg_id" value="<?= $msg['message_id'] ?>">
                                                        <button type="submit" class="btn-action-icon" title="Mark as Read"><i class="fa-regular fa-envelope"></i></button>
                                                    </form>
                                                <?php else: ?>
                                                    <button class="btn-action-icon btn-icon-disabled" title="Read"><i class="fa-regular fa-envelope-open"></i></button>
                                                <?php endif; ?>

                                                <form action="admin_messages.php" method="POST" style="margin: 0; display: inline;">
                                                    <input type="hidden" name="delete_msg" value="1">
                                                    <input type="hidden" name="msg_id" value="<?= $msg['message_id'] ?>">
                                                    <button type="submit" class="btn-action-icon btn-delete-icon" onclick="return confirm('Delete this message?')" title="Delete"><i class="fa-regular fa-trash-can"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($messages)): ?>
                                    <tr>
                                        <td colspan="5" style="text-align: center;">No messages found.</td>
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
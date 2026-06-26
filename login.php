<?php
require_once 'config/config.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['full_name'];

        if ($user['role'] === 'admin') {
            header("Location: admin/admin_dashboard.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fatima Store- Login</title>

    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body >
    <?php include 'includes/header.php'; ?>

    <div class="login-body">
    <div class="login-page-wrapper">

        <div class="login-brand">
            <div class="brand-name"><a href="index.php" style="color: #000000;text-decoration:none;">Fatima Store</a></div>
            <div class="brand-subtitle">PREMIUM ELECTRONICS</div>
        </div>

        <div class="login-card">
            <h2>Welcome Back</h2>
            <p class="login-subtitle">Enter your credentials to access your TechNova account.</p>

            <?php if ($error): ?>
                <div style="color: red; margin-bottom: 15px; text-align: center;"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST">

                <div class="input-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="name@gmail.com" required>
                </div>

                <div class="input-group">
                    <div class="password-labels">
                        <label for="password">Password</label>
                    </div>

                    <div class="password-input-wrapper">
                        <input type="password" id="password" name="password" required>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    Login <i class="fa-solid fa-arrow-right-to-bracket"></i>
                </button>

            </form>

            <div class="login-footer"> Don't have an account? <a href="register.php">Register here</a>
            </div>
        </div>

    </div>
</div>
</body>

</html>
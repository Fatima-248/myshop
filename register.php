<?php
require_once 'config/config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email is already registered!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, password) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$fullname, $email, $phone, $hashed_password])) {
                $success = "Registration successful! You can now <a href='login.php'>login</a>.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fatima Store - Register</title>
    <link rel="stylesheet" href="css/header.css">

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
    <?php include 'includes/header.php'; ?>

<body >
<div class="register-body">
    <div class="register-page-wrapper">


        <div class="register-card">
            <h2>Join Fatima Store</h2>
            <p class="register-subtitle">Step into the future of precision electronics.</p>

            <?php if ($error): ?>
                <div style="color: red; margin-bottom: 15px; text-align: center;"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div style="color: green; margin-bottom: 15px; text-align: center;"><?php echo $success; ?></div>
            <?php endif; ?>

            <form action="register.php" method="POST">

                <div class="input-group">
                    <label for="fullname">Full Name</label>
                    <div class="input-icon-wrapper">
                        <i class="fa-regular fa-user input-icon"></i>
                        <input type="text" id="fullname" name="fullname" placeholder=" name" required>
                    </div>
                </div>

                <div class="input-group">
                    <label for="email">Email Address</label>
                    <div class="input-icon-wrapper">
                        <i class="fa-regular fa-envelope input-icon"></i>
                        <input type="email" id="email" name="email" placeholder="name@gmail.com" required>
                    </div>
                </div>

                <div class="input-group">
                    <label for="phone">Phone Number</label>
                    <div class="input-icon-wrapper">
                        <i class="fa-solid fa-phone-volume input-icon"></i>
                        <input type="tel" id="phone" name="phone" placeholder="+972/+970 0000000" required>
                    </div>
                </div>

                <div class="input-group">
                    <label for="password">Password</label>
                    <div class="input-icon-wrapper">
                        <i class="fa-solid fa-lock input-icon"></i>
                        <input type="password" id="password" name="password" placeholder="Min. 8 characters" required>
                    </div>
                </div>

                <div class="input-group">
                    <label for="confirm-password">Confirm Password</label>
                    <div class="input-icon-wrapper">
                        <i class="fa-solid fa-key input-icon"></i>
                        <input type="password" id="confirm-password" name="confirm_password" placeholder="Repeat your password" required>
                    </div>
                </div>

                <button type="submit" class="btn-register">
                    Create Account <i class="fa-solid fa-arrow-right"></i>
                </button>

            </form>

            <div class="register-footer">
                Already have an account? <a href="login.php">Login</a>
            </div>

        </div>
    </div>
</div>
</body>

</html>
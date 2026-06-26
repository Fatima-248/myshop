<?php
session_start();
require_once 'config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']); 
    $message = trim($_POST['message']);

    if (empty($name) || empty($email) || empty($message)) {
        $_SESSION['contact_error'] = "All fields are required.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO contacts (name, email, subject, message, is_read) VALUES (?, ?, ?, ?, 0)");
        if ($stmt->execute([$name, $email, $subject, $message])) {
            $_SESSION['contact_success'] = "Your message has been sent successfully! We will get back to you soon.";
        } else {
            $_SESSION['contact_error'] = "Failed to send message. Please try again.";
        }
    }
}
header("Location: contact.php");
exit();
?>

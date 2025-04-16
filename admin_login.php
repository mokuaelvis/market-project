<?php
session_start();
require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Check admin credentials
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ? AND is_active = 1");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    
    if ($admin && password_verify($password, $admin['password_hash'])) {
        // Set admin session
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_role'] = $admin['role'];
        
        // Redirect to admin dashboard
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $_SESSION['message'] = "Invalid admin username or password";
        $_SESSION['message_type'] = 'danger';
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>
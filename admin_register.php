<?php
session_start();
require_once 'db_config.php';

// Only allow if super admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'superadmin') {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $name = $_POST['name'];
    
    try {
        $stmt = $pdo->prepare("INSERT INTO admins (username, password_hash, email, name) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $password, $email, $name]);
        
        $_SESSION['message'] = "Admin account created successfully";
        $_SESSION['message_type'] = 'success';
        header("Location: admin_dashboard.php");
        exit();
    } catch (PDOException $e) {
        $error = "Error creating admin account: " . $e->getMessage();
    }
}
?>

<!-- Registration form HTML similar to login page -->
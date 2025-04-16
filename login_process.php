<?php
session_start();
require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM clients WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            // Login successful
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_store'] = $user['store_number'];
            
            // Redirect to dashboard
            header('Location: dashboard.php');
            exit();
        } else {
            // Login failed
            $_SESSION['message'] = 'Invalid email or password.';
            $_SESSION['message_type'] = 'danger';
            header('Location: login.php');
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = 'Login error: ' . $e->getMessage();
        $_SESSION['message_type'] = 'danger';
        header('Location: login.php');
        exit();
    }
} else {
    header('Location: login.php');
    exit();
}
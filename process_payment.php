<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_id = $_SESSION['user_id'];
    $amount = $_POST['amount'];
    $method = $_POST['payment_method'];
    $reference = trim($_POST['reference_number']);

    // Database connection
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=marketproject", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("INSERT INTO payments (client_id, amount, payment_method, reference_number, status) VALUES (?, ?, ?, ?, 'pending')");
        $stmt->execute([$client_id, $amount, $method, $reference]);

        $_SESSION['payment_message'] = "Payment submitted successfully. Awaiting confirmation.";
        $_SESSION['payment_type'] = "success";

    } catch (PDOException $e) {
        $_SESSION['payment_message'] = "Error processing payment: " . $e->getMessage();
        $_SESSION['payment_type'] = "danger";
    }

    header("Location: dashboard.php");
    exit;
}
?>

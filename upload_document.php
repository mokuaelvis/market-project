<?php
// upload_document.php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Database connection
$host = 'localhost';
$dbname = 'marketproject';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['additional_document'])) {
    $uploadDir = 'uploads/documents/';
    
    // Create directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $allowedExtensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    $file = $_FILES['additional_document'];
    
    // Check for errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['message'] = "Upload error: " . $file['error'];
        $_SESSION['message_type'] = 'danger';
        header("Location: dashboard.php");
        exit();
    }

    // Check file size
    if ($file['size'] > $maxSize) {
        $_SESSION['message'] = "File too large. Maximum size is 5MB";
        $_SESSION['message_type'] = 'danger';
        header("Location: dashboard.php");
        exit();
    }

    // Get file extension
    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    // Validate extension
    if (!in_array($fileExt, $allowedExtensions)) {
        $_SESSION['message'] = "Invalid file type. Allowed: " . implode(', ', $allowedExtensions);
        $_SESSION['message_type'] = 'danger';
        header("Location: dashboard.php");
        exit();
    }

    // Generate unique filename
    $fileName = uniqid() . '_' . bin2hex(random_bytes(8)) . '.' . $fileExt;
    $targetPath = $uploadDir . $fileName;

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        // Store in database (you'll need to add a documents table)
        try {
            $stmt = $pdo->prepare("INSERT INTO client_documents 
                                  (client_id, document_path, document_type, uploaded_at) 
                                  VALUES (?, ?, ?, NOW())");
            $stmt->execute([
                $_SESSION['user_id'],
                $targetPath,
                $fileExt
            ]);
            
            $_SESSION['message'] = "Document uploaded successfully!";
            $_SESSION['message_type'] = 'success';
        } catch (PDOException $e) {
            $_SESSION['message'] = "Database error: " . $e->getMessage();
            $_SESSION['message_type'] = 'danger';
        }
    } else {
        $_SESSION['message'] = "Failed to upload document";
        $_SESSION['message_type'] = 'danger';
    }

    header("Location: dashboard.php");
    exit();
}
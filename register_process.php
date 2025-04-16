<?php
// register_process.php
session_start();

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

/**
 * Handles file upload with validation
 */
function handleFileUpload($file, $uploadDir, $allowedExtensions, $maxSize = 2097152) {
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [
            'success' => false,
            'message' => 'File upload error: ' . $file['error'],
            'name' => ''
        ];
    }

    // Check file size
    if ($file['size'] > $maxSize) {
        return [
            'success' => false,
            'message' => 'File is too large. Maximum size: ' . ($maxSize / 1024 / 1024) . 'MB',
            'name' => ''
        ];
    }

    // Get file extension
    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    // Validate extension
    if (!in_array($fileExt, $allowedExtensions)) {
        return [
            'success' => false,
            'message' => 'Invalid file type. Allowed: ' . implode(', ', $allowedExtensions),
            'name' => ''
        ];
    }

    // Create upload directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Generate unique filename
    $fileName = uniqid() . '_' . bin2hex(random_bytes(8)) . '.' . $fileExt;
    $targetPath = $uploadDir . $fileName;

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return [
            'success' => true,
            'message' => 'File uploaded successfully',
            'name' => $fileName
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Failed to move uploaded file',
            'name' => ''
        ];
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $errors = [];
    
    // Check required fields
    $requiredFields = ['name', 'email', 'phone', 'password', 'confirm_password', 'id_number', 'store_number'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
        }
    }

    // Check if passwords match
    if ($_POST['password'] !== $_POST['confirm_password']) {
        $errors[] = "Passwords do not match";
    }

    // Validate email format
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM clients WHERE email = ?");
    $stmt->execute([$_POST['email']]);
    if ($stmt->fetch()) {
        $errors[] = "Email already registered";
    }

    // Check if ID number already exists
    $stmt = $pdo->prepare("SELECT id FROM clients WHERE id_number = ?");
    $stmt->execute([$_POST['id_number']]);
    if ($stmt->fetch()) {
        $errors[] = "ID number already registered";
    }

    // Check if store number already exists
    $stmt = $pdo->prepare("SELECT id FROM clients WHERE store_number = ?");
    $stmt->execute([$_POST['store_number']]);
    if ($stmt->fetch()) {
        $errors[] = "Store number already taken";
    }

    // Handle file uploads
    $photoDir = 'uploads/photos/';
    $docDir = 'uploads/documents/';
    
    // Process photo upload
    $photoUpload = handleFileUpload($_FILES['photo'], $photoDir, ['jpg', 'jpeg', 'png', 'gif']);
    if (!$photoUpload['success']) {
        $errors[] = 'Photo upload failed: ' . $photoUpload['message'];
    }
    $photoPath = $photoDir . $photoUpload['name'];
    
    // Process document upload
    $docUpload = handleFileUpload($_FILES['document'], $docDir, ['pdf', 'doc', 'docx']);
    if (!$docUpload['success']) {
        $errors[] = 'Document upload failed: ' . $docUpload['message'];
    }
    $docPath = $docDir . $docUpload['name'];

    // If no errors, proceed with registration
    if (empty($errors)) {
        $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO clients 
                (name, email, phone, password, id_number, store_number, photo_path, document_path, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            
            $stmt->execute([
                $_POST['name'],
                $_POST['email'],
                $_POST['phone'],
                $hashedPassword,
                $_POST['id_number'],
                $_POST['store_number'],
                $photoPath,
                $docPath
            ]);

            // Get the new client ID
            $clientId = $pdo->lastInsertId();

            // Set session variables
            $_SESSION['user_id'] = $clientId;
            $_SESSION['user_name'] = $_POST['name'];
            $_SESSION['user_email'] = $_POST['email'];
            $_SESSION['user_role'] = 'client';
            $_SESSION['photo_path'] = $photoPath;

            // Redirect to dashboard
            header("Location: dashboard.php");
            exit();
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
            $_SESSION['message'] = implode('<br>', $errors);
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php");
            exit();
        }
    } else {
        // Store errors in session and redirect back
        $_SESSION['message'] = implode('<br>', $errors);
        $_SESSION['message_type'] = 'danger';
        header("Location: index.php");
        exit();
    }
}
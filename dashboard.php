 <?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Please login to access the dashboard";
    $_SESSION['message_type'] = 'warning';
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

// Get client data
$stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$client = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$client) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// Function to get file icon based on extension
function getFileIcon($path) {
    $ext = pathinfo($path, PATHINFO_EXTENSION);
    switch(strtolower($ext)) {
        case 'pdf':
            return 'bi-file-earmark-pdf';
        case 'doc':
        case 'docx':
            return 'bi-file-earmark-word';
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif':
            return 'bi-file-earmark-image';
        default:
            return 'bi-file-earmark';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard | Ongata Rongai Market</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #e74c3c;
            --accent-color: #3498db;
            --light-color: #ecf0f1;
            --dark-color: #34495e;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
        }
        
        .sidebar {
            background-color: var(--primary-color);
            color: white;
            height: 100vh;
            position: fixed;
            padding-top: 20px;
        }
        
        .sidebar .nav-link {
            color: var(--light-color);
            margin-bottom: 5px;
            border-radius: 5px;
            padding: 10px 15px;
        }
        
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        
        .profile-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid var(--light-color);
        }
        
        .document-card {
            transition: transform 0.3s;
        }
        
        .document-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .document-icon {
            font-size: 3rem;
            color: var(--secondary-color);
        }
        
        .alert {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Check for session messages -->
    <?php if(isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['message_type'] ?? 'info'; ?>">
            <?php echo $_SESSION['message']; ?>
            <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
        </div>
    <?php endif; ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="text-center mb-4">
                    <?php if (!empty($client['photo_path'])): ?>
                        <img src="<?php echo $client['photo_path']; ?>" alt="Profile Photo" class="profile-img mb-3">
                    <?php else: ?>
                        <div class="profile-img mb-3 bg-secondary d-flex align-items-center justify-content-center">
                            <i class="bi bi-person" style="font-size: 3rem; color: white;"></i>
                        </div>
                    <?php endif; ?>
                    <h4><?php echo htmlspecialchars($client['name']); ?></h4>
                    <p class="text-muted">Client</p>
                </div>
                
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                  
                   
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="payments.php">
                            <i class="bi bi-credit-card"></i> Payments
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="notifications.php">
                            <i class="bi bi-bell"></i> Notifications
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary">Share</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
                            <i class="bi bi-calendar"></i> This week
                        </button>
                    </div>
                </div>
                <!-- Notifications Section -->
<div class="profile-card">
    <h3>Recent Notifications</h3>
    <ul class="list-group">
        <?php
        $notifStmt = $pdo->prepare("SELECT * FROM notifications WHERE client_id = ? ORDER BY created_at DESC LIMIT 5");
        ;
        $notifications = $notifStmt->fetchAll();

        if (count($notifications) > 0):
            foreach ($notifications as $note): ?>
                <li class="list-group-item list-group-item-<?php echo htmlspecialchars($note['type']); ?>">
                    <?php echo htmlspecialchars($note['message']); ?>
                    <small class="d-block text-muted"><?php echo date('d M Y, H:i', strtotime($note['created_at'])); ?></small>
                </li>
            <?php endforeach;
        else: ?>
            <li class="list-group-item text-muted">No notifications yet.</li>
        <?php endif; ?>
    </ul>
</div>

                
                <!-- Welcome Card -->
                <div class="profile-card">
                    <div class="row">
                        <div class="col-md-8">
                            <h2>Welcome, <?php echo htmlspecialchars($client['name']); ?>!</h2>
                            <p class="lead">Here's your market management dashboard where you can manage your stall, payments, and profile.</p>
                            <div class="row mt-4">
                                <div class="col-md-4">
                                    <div class="card text-white bg-primary mb-3">
                                        <div class="card-body">
                                            <h5 class="card-title">Stall Number</h5>
                                            <p class="card-text h3"><?php echo htmlspecialchars($client['store_number']); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card text-white bg-success mb-3">
                                        <div class="card-body">
                                            <h5 class="card-title">Account Status</h5>
                                            <p class="card-text h3">Active</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card text-white bg-info mb-3">
                                        <div class="card-body">
                                            <h5 class="card-title">Member Since</h5>
                                            <p class="card-text h3">
                                                <?php echo date('M Y', strtotime($client['created_at'])); ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <?php if (!empty($client['photo_path'])): ?>
                                <img src="<?php echo $client['photo_path']; ?>" alt="Profile Photo" class="img-fluid rounded" style="max-height: 200px;">
                            <?php else: ?>
                                <div class="bg-light p-5 rounded">
                                    <i class="bi bi-person" style="font-size: 3rem;"></i>
                                    <p class="mt-2">No photo uploaded</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Documents Section -->
                <div class="profile-card">
                    <h3>My Documents</h3>
                    <div class="row">
                        <!-- Profile Photo -->
                        <div class="col-md-4 mb-4">
                            <div class="card document-card h-100">
                                <div class="card-body text-center">
                                    <i class="bi bi-file-earmark-image document-icon"></i>
                                    <h5 class="card-title mt-3">Profile Photo</h5>
                                    <?php if (!empty($client['photo_path'])): ?>
                                        <img src="<?php echo $client['photo_path']; ?>" class="img-thumbnail mt-2 mb-3" style="max-height: 150px;">
                                        <a href="<?php echo $client['photo_path']; ?>" class="btn btn-sm btn-primary" download>Download</a>
                                        <a href="<?php echo $client['photo_path']; ?>" class="btn btn-sm btn-secondary" target="_blank">View</a>
                                    <?php else: ?>
                                        <p class="text-muted">No photo uploaded</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Uploaded Document -->
                        <div class="col-md-4 mb-4">
                            <div class="card document-card h-100">
                                <div class="card-body text-center">
                                    <i class="bi <?php echo getFileIcon($client['document_path']); ?> document-icon"></i>
                                    <h5 class="card-title mt-3">Registration Document</h5>
                                    <?php if (!empty($client['document_path'])): ?>
                                        <p class="text-muted mt-3">
                                            <?php echo pathinfo($client['document_path'], PATHINFO_BASENAME); ?>
                                        </p>
                                        <div class="mt-3">
                                            <a href="<?php echo $client['document_path']; ?>" class="btn btn-sm btn-primary" download>Download</a>
                                            <a href="<?php echo $client['document_path']; ?>" class="btn btn-sm btn-secondary" target="_blank">View</a>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted">No document uploaded</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <!-- Payment History Section -->


                        
                       
                       <!-- Additional Documents -->
<div class="col-md-4 mb-4">
    <div class="card document-card h-100">
        <div class="card-body text-center">
            <i class="bi bi-file-earmark-plus document-icon"></i>
            <h5 class="card-title mt-3">Upload Additional Document</h5>
            <form action="upload_document.php" method="POST" enctype="multipart/form-data" class="mt-4">
                <div class="mb-3">
                    <input type="file" class="form-control" name="additional_document" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                    <small class="text-muted">Max 5MB (PDF, Word, Images)</small>
                </div>
                <button type="submit" class="btn btn-sm btn-success">Upload</button>
            </form>
            
            <!-- Display existing additional documents -->
            <?php
            $docStmt = $pdo->prepare("SELECT * FROM client_documents WHERE client_id = ? ORDER BY uploaded_at DESC");
           
            $additionalDocs = $docStmt->fetchAll();
            
            if (!empty($additionalDocs)): ?>
                <hr>
                <h6>Additional Documents</h6>
                <div class="list-group mt-2">
                    <?php foreach ($additionalDocs as $doc): ?>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="bi <?php echo getFileIcon($doc['document_path']); ?>"></i>
                                    <?php echo pathinfo($doc['document_path'], PATHINFO_BASENAME); ?>
                                </div>
                                <div>
                                    <a href="<?php echo $doc['document_path']; ?>" class="btn btn-sm btn-outline-primary" download>
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <a href="<?php echo $doc['document_path']; ?>" class="btn btn-sm btn-outline-secondary" target="_blank">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
  <!-- Payment History Section -->
<div class="profile-card">
    <h3>My Payments</h3>

    <?php
    $paymentStmt = $pdo->prepare("SELECT * FROM payments WHERE client_id = ? ORDER BY paid_at DESC");
   
    $payments = $paymentStmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <?php if (count($payments) > 0): ?>
        <div class="table-responsive mt-3">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Reference</th>
                        <th>Status</th>
                        <th>Paid At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $index => $payment): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td>KES <?php echo number_format($payment['amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                            <td><?php echo htmlspecialchars($payment['reference_number']); ?></td>
                            <td>
                                <?php
                                $statusClass = match ($payment['status']) {
                                    'confirmed' => 'success',
                                    'pending' => 'warning',
                                    'failed' => 'danger',
                                    default => 'secondary'
                                };
                                ?>
                                <span class="badge bg-<?php echo $statusClass; ?>">
                                    <?php echo ucfirst($payment['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d M Y, H:i', strtotime($payment['paid_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-muted mt-3">You have not made any payments yet.</p>
    <?php endif; ?>
</div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
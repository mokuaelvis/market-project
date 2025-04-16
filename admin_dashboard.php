<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['admin_id'])) {
    $_SESSION['message'] = "Please login to access the admin dashboard";
    $_SESSION['message_type'] = 'warning';
    header("Location: admin_login.php");
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

// Get admin data
$stmt = $pdo->prepare("SELECT * FROM admins WHERE username = 'admin';");
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin) {
    session_destroy();
    header("Location: admin_login.php");
    exit();
}

// Get stats for dashboard
$stats = $pdo->query("
    SELECT 
        (SELECT COUNT(*) FROM clients) as total_clients,
        (SELECT COUNT(*) FROM clients WHERE status = 'active') as active_clients,
        (SELECT COUNT(*) FROM clients WHERE status = 'pending') as pending_clients,
        (SELECT COUNT(*) FROM payments WHERE status = 'pending') as pending_payments,
        (SELECT COUNT(*) FROM documents WHERE status = 'pending') as pending_documents
")->fetch(PDO::FETCH_ASSOC);

// Get recent clients
$recentClients = $pdo->query("SELECT * FROM clients ORDER BY created_at DESC LIMIT 5")->fetchAll();

// Get pending payments
$pendingPayments = $pdo->query("
    SELECT p.*, c.name as client_name 
    FROM payments p
    JOIN clients c ON p.client_id = c.id
    WHERE p.status = 'pending'
    ORDER BY p.payment_date DESC
    LIMIT 5
")->fetchAll();

// Get pending documents
$pendingDocuments = $pdo->query("
    SELECT d.*, c.name as client_name 
    FROM documents d
    JOIN clients c ON d.client_id = c.id
    WHERE d.status = 'pending'
    ORDER BY d.uploaded_at DESC
    LIMIT 5
")->fetchAll();

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
    <title>Admin Dashboard | Ongata Rongai Market</title>
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
        
        .dashboard-card {
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
        
        .stat-card {
            transition: transform 0.3s;
            height: 100%;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }
        
        .alert {
            margin-bottom: 20px;
        }
        
        .badge {
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .badge-pending {
            background-color: #ffc107;
            color: #212529;
        }
        
        .badge-approved {
            background-color: #28a745;
            color: white;
        }
        
        .badge-rejected {
            background-color: #dc3545;
            color: white;
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
                    <?php if (!empty($admin['photo_path'])): ?>
                        <img src="<?php echo $admin['photo_path']; ?>" alt="Profile Photo" class="profile-img mb-3">
                    <?php else: ?>
                        <div class="profile-img mb-3 bg-secondary d-flex align-items-center justify-content-center">
                            <i class="bi bi-person" style="font-size: 3rem; color: white;"></i>
                        </div>
                    <?php endif; ?>
                    <h4><?php echo htmlspecialchars($admin['name']); ?></h4>
                    <p class="text-muted">Administrator</p>
                </div>
                
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="admin_dashboard.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_clients.php">
                            <i class="bi bi-people"></i> Manage Clients
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_payments.php">
                            <i class="bi bi-credit-card"></i> Payment Approvals
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_documents.php">
                            <i class="bi bi-files"></i> Document Verification
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_reports.php">
                            <i class="bi bi-graph-up"></i> Reports
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_settings.php">
                            <i class="bi bi-gear"></i> Settings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_logout.php">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Admin Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button type="button" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-calendar"></i> <?php echo date('F j, Y'); ?>
                        </button>
                    </div>
                </div>
                
                <!-- Welcome Card -->
                <div class="dashboard-card mb-4">
                    <div class="row">
                        <div class="col-md-8">
                            <h2>Welcome, <?php echo htmlspecialchars($admin['name']); ?>!</h2>
                            <p class="lead">Market administration dashboard for managing clients, payments, and documents.</p>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="bg-light p-4 rounded">
                                <i class="bi bi-shield-lock" style="font-size: 3rem; color: var(--primary-color);"></i>
                                <p class="mt-2">Admin Privileges</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="card stat-card text-white bg-primary">
                            <div class="card-body text-center">
                                <i class="bi bi-people stat-icon"></i>
                                <h3><?php echo $stats['total_clients']; ?></h3>
                                <p class="card-text">Total Clients</p>
                                <a href="admin_clients.php" class="btn btn-sm btn-light">View All</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card stat-card text-white bg-success">
                            <div class="card-body text-center">
                                <i class="bi bi-check-circle stat-icon"></i>
                                <h3><?php echo $stats['active_clients']; ?></h3>
                                <p class="card-text">Active Clients</p>
                                <a href="admin_clients.php?status=active" class="btn btn-sm btn-light">View Active</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card stat-card text-white bg-warning">
                            <div class="card-body text-center">
                                <i class="bi bi-hourglass-split stat-icon"></i>
                                <h3><?php echo $stats['pending_clients']; ?></h3>
                                <p class="card-text">Pending Clients</p>
                                <a href="admin_clients.php?status=pending" class="btn btn-sm btn-light">Review</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <div class="card stat-card text-white bg-info">
                            <div class="card-body text-center">
                                <i class="bi bi-credit-card stat-icon"></i>
                                <h3><?php echo $stats['pending_payments']; ?></h3>
                                <p class="card-text">Pending Payments</p>
                                <a href="admin_payments.php" class="btn btn-sm btn-light">Review Payments</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="card stat-card text-white bg-danger">
                            <div class="card-body text-center">
                                <i class="bi bi-files stat-icon"></i>
                                <h3><?php echo $stats['pending_documents']; ?></h3>
                                <p class="card-text">Pending Documents</p>
                                <a href="admin_documents.php" class="btn btn-sm btn-light">Verify Documents</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Activity Section -->
                <div class="row">
                    <!-- Recent Clients -->
                    <div class="col-md-6">
                        <div class="dashboard-card">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h3>Recent Clients</h3>
                                <a href="admin_clients.php" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentClients as $client): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($client['name']); ?></td>
                                            <td>
                                                <span class="badge badge-<?php 
                                                    echo $client['status'] == 'active' ? 'approved' : 
                                                         ($client['status'] == 'pending' ? 'pending' : 'rejected'); 
                                                ?>">
                                                    <?php echo ucfirst($client['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="admin_view_client.php?id=<?php echo $client['id']; ?>" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pending Payments -->
                    <div class="col-md-6">
                        <div class="dashboard-card">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h3>Pending Payments</h3>
                                <a href="admin_payments.php" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Client</th>
                                            <th>Amount</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pendingPayments as $payment): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($payment['client_name']); ?></td>
                                            <td>Ksh <?php echo number_format($payment['amount'], 2); ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="admin_approve_payment.php?id=<?php echo $payment['id']; ?>" class="btn btn-success">
                                                        <i class="bi bi-check"></i> Approve
                                                    </a>
                                                    <a href="admin_reject_payment.php?id=<?php echo $payment['id']; ?>" class="btn btn-danger">
                                                        <i class="bi bi-x"></i> Reject
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Pending Documents -->
                <div class="dashboard-card mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3>Pending Document Verification</h3>
                        <a href="admin_documents.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Client</th>
                                    <th>Document</th>
                                    <th>Type</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendingDocuments as $doc): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($doc['client_name']); ?></td>
                                    <td>
                                        <i class="bi <?php echo getFileIcon($doc['document_path']); ?>"></i>
                                        <?php echo pathinfo($doc['document_path'], PATHINFO_BASENAME); ?>
                                    </td>
                                    <td><?php echo ucfirst($doc['document_type']); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?php echo $doc['document_path']; ?>" target="_blank" class="btn btn-primary">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                            <a href="admin_approve_document.php?id=<?php echo $doc['id']; ?>" class="btn btn-success">
                                                <i class="bi bi-check"></i> Approve
                                            </a>
                                            <a href="admin_reject_document.php?id=<?php echo $doc['id']; ?>" class="btn btn-danger">
                                                <i class="bi bi-x"></i> Reject
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
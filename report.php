<?php
session_start();


$host = "localhost";
$user = "root";
$password = "";
$db = "marketproject";

$data = mysqli_connect($host, $user, $password, $db);

if (!$data) {
    die("Connection failed: " . mysqli_connect_error());
}

// Functions
function getTotalRegistrations($conn) {
    $sql = "SELECT COUNT(*) as total FROM user WHERE usertype='client'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}

function getTodaysRegistrations($conn) {
    $today = date('Y-m-d');
    $sql = "SELECT COUNT(*) as total FROM user WHERE usertype='client' AND DATE(created_at) = '$today'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}

function getUserTypeDistribution($conn) {
    $sql = "SELECT usertype, COUNT(*) as count FROM user GROUP BY usertype";
    $result = mysqli_query($conn, $sql);
    $distribution = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $distribution[$row['usertype']] = $row['count'];
    }
    return $distribution;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Reports - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            color: var(--dark-color);
        }
        
        .header {
            background-color: var(--primary-color);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .header a {
            color: white;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .logout .btn {
            background-color: var(--secondary-color);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 4px;
            transition: all 0.3s;
        }
        
        .logout .btn:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
        }
        
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        h1, h2, h3 {
            color: var(--primary-color);
        }
        
        .stat-card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-left: 4px solid var(--accent-color);
            transition: all 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .stat-card h3 {
            font-size: 1rem;
            color: var(--dark-color);
            margin-bottom: 10px;
        }
        
        .stat-card p {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--secondary-color);
            margin: 0;
        }
        
        .report-actions {
            margin: 20px 0;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .report-actions .btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
        }
        
        .report-actions .btn:hover {
            background-color: var(--dark-color);
        }
        
        .chart-container {
            margin: 30px 0;
            height: 400px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: var(--primary-color);
            color: white;
            position: sticky;
            top: 0;
        }
        
        tr:hover {
            background-color: #f5f5f5;
        }
        
        .thumbnail {
            max-width: 50px;
            max-height: 50px;
            border-radius: 4px;
        }
        
        .file-link {
            color: var(--accent-color);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .file-link:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }
        
        .page-link {
            padding: 8px 16px;
            margin: 0 4px;
            border: 1px solid #ddd;
            color: var(--primary-color);
            text-decoration: none;
            border-radius: 4px;
            transition: all 0.3s;
        }
        
        .page-link:hover {
            background-color: #f5f5f5;
        }
        
        .current-page {
            padding: 8px 16px;
            margin: 0 4px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 4px;
        }
        
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: var(--accent-color);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .back-link:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            th, td {
                padding: 8px;
                font-size: 0.9rem;
            }
            
            .report-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <a href="admin_dashboard.php">Admin Dashboard</a>
        <div class="logout">
            <a href="logout.php" class="btn btn-primary">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </header>
    
    <div class="container">
        <h1><i class="bi bi-graph-up"></i> Registration Reports</h1>
        
        <div class="report-actions">
            <a href="generate_report.php?type=txt" class="btn">
                <i class="bi bi-file-earmark-text"></i> Download TXT Report
            </a>
            <a href="generate_report.php?type=csv" class="btn">
                <i class="bi bi-file-earmark-spreadsheet"></i> Download CSV Report
            </a>
            <a href="generate_report.php?type=pdf" class="btn">
                <i class="bi bi-file-earmark-pdf"></i> Download PDF Report
            </a>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="stat-card">
                    <h3><i class="bi bi-people"></i> Total Client Registrations</h3>
                    <p><?php echo getTotalRegistrations($data); ?></p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-card">
                    <h3><i class="bi bi-calendar-day"></i> Today's Registrations</h3>
                    <p><?php echo getTodaysRegistrations($data); ?></p>
                </div>
            </div>
        </div>
        
        <div class="chart-container">
            <canvas id="userTypeChart"></canvas>
        </div>
        
        <h2><i class="bi bi-list-ul"></i> Recent Client Registrations</h2>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>ID Number</th>
                        <th>Store Number</th>
                        <th>Photo</th>
                        <th>Documents</th>
                        <th>Registration Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $perPage = 10;
                    $start = ($page - 1) * $perPage;
                    
                    $sql = "SELECT * FROM user WHERE usertype='client' ORDER BY created_at DESC LIMIT $start, $perPage";
                    $result = mysqli_query($data, $sql);
                    
                    if (mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            // Handle photo display
                            $photoCell = '-';
                            if (!empty($row['photo_path'])) {
                                $photoCell = '<a href="'.htmlspecialchars($row['photo_path']).'" class="file-link" target="_blank">';
                                if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $row['photo_path'])) {
                                    $photoCell .= '<img src="'.htmlspecialchars($row['photo_path']).'" class="thumbnail" alt="User photo">';
                                } else {
                                    $photoCell .= 'View Photo';
                                }
                                $photoCell .= '</a>';
                            }
                            
                            // Handle documents display
                            $docCell = '-';
                            if (!empty($row['document_path'])) {
                                $docs = explode(',', $row['document_path']);
                                $docCell = '';
                                foreach ($docs as $doc) {
                                    $doc = trim($doc);
                                    if (!empty($doc)) {
                                        $docCell .= '<a href="'.htmlspecialchars($doc).'" class="file-link" target="_blank">Document</a><br>';
                                    }
                                }
                            }
                            
                            echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['username']}</td>
                                <td>{$row['email']}</td>
                                <td>{$row['phone']}</td>
                                <td>{$row['id_number']}</td>
                                <td>{$row['store_number']}</td>
                                <td>{$photoCell}</td>
                                <td>{$docCell}</td>
                                <td>{$row['created_at']}</td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9'>No registrations found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        
        <div class="pagination">
            <?php
            $totalRecords = getTotalRegistrations($data);
            $totalPages = ceil($totalRecords / $perPage);
            
            if ($totalPages > 1) {
                if ($page > 1) {
                    echo "<a href='reports.php?page=".($page-1)."' class='page-link'><i class='bi bi-chevron-left'></i> Previous</a> ";
                }
                
                for ($i = 1; $i <= $totalPages; $i++) {
                    if ($i == $page) {
                        echo "<span class='current-page'>{$i}</span> ";
                    } else {
                        echo "<a href='reports.php?page={$i}' class='page-link'>{$i}</a> ";
                    }
                }
                
                if ($page < $totalPages) {
                    echo "<a href='reports.php?page=".($page+1)."' class='page-link'>Next <i class='bi bi-chevron-right'></i></a> ";
                }
            }
            ?>
        </div>
        
        <a href="admin_dashboard.php" class="back-link">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>
    
    <script>
        // User type distribution chart
        const ctx = document.getElementById('userTypeChart').getContext('2d');
        const userTypeData = <?php echo json_encode(getUserTypeDistribution($data)); ?>;
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(userTypeData),
                datasets: [{
                    data: Object.values(userTypeData),
                    backgroundColor: [
                        '#e74c3c',
                        '#3498db',
                        '#2ecc71',
                        '#f39c12',
                        '#9b59b6',
                        '#1abc9c'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    title: {
                        display: true,
                        text: 'User Type Distribution',
                        font: {
                            size: 18
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
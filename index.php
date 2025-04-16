<?php
session_start();

// Check for session messages
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'] ?? 'info';
    echo "<div class='alert alert-$message_type'>$message</div>";
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ongata Rongai Market Management</title>
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
            color: var(--dark-color);
        }
        
        .navbar {
            background-color: var(--primary-color);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            color: white !important;
            font-size: 1.5rem;
        }
        
        .nav-link {
            color: var(--light-color) !important;
            font-weight: 500;
            margin: 0 10px;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            color: var(--secondary-color) !important;
        }
        
        .btn-market {
            background-color: var(--secondary-color);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 4px;
            transition: all 0.3s ease;
        }
        
        .btn-market:hover {
            background-color: #c0392b;
            color: white;
            transform: translateY(-2px);
        }
        
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('market.jpg');
            background-size: cover;
            background-position: center;
            height: 60vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
        }
        
        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
        }
        
        .hero-subtitle {
            font-size: 1.5rem;
            margin-bottom: 30px;
        }
        
        .auth-container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .auth-tabs {
            display: flex;
            border-bottom: 1px solid #ddd;
        }
        
        .auth-tab {
            flex: 1;
            text-align: center;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #f8f9fa;
            font-weight: 600;
        }
        
        .auth-tab.active {
            background: white;
            color: var(--secondary-color);
            border-bottom: 3px solid var(--secondary-color);
        }
        
        .auth-content {
            padding: 30px;
        }
        
        .auth-form {
            display: none;
        }
        
        .auth-form.active {
            display: block;
        }
        
        .form-control {
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        
        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
        }
        
        .features-section {
            padding: 60px 0;
            background-color: white;
        }
        
        .feature-card {
            text-align: center;
            padding: 30px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
            margin-bottom: 30px;
            border: 1px solid #eee;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .feature-icon {
            font-size: 2.5rem;
            color: var(--secondary-color);
            margin-bottom: 20px;
        }
        
        footer {
            background-color: var(--primary-color);
            color: white;
            padding: 30px 0;
            text-align: center;
        }
        
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.2rem;
            }
            
            .auth-container {
                margin: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Ongata Rongai Market</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Stalls</a>
                    </li>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <!-- Show these when user is logged in -->
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn-market" href="logout.php">Logout</a>
                        </li>
                    <?php elseif(isset($_SESSION['admin_id'])): ?>
                        <!-- Show these when admin is logged in -->
                        <li class="nav-item">
                            <a class="nav-link" href="admin_dashboard.php">Admin Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn-market" href="admin_logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <!-- Show these when no one is logged in -->
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="showRegisterForm">Registration</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn-market" href="admin_login.php" id="authToggle">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn-market" href="admin_login.php" id="adminAuthToggle">Admin Login</a>
                        </li>
                      


                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="hero-title">Ongata Rongai Market</h1>
            <p class="hero-subtitle">Your one-stop marketplace for fresh produce and goods</p>
            <a href="#" class="btn btn-danger btn-lg" id="joinNowBtn">Join Now</a>
        </div>
    </section>

    <!-- Authentication Container -->
    <div class="auth-container" id="authContainer" style="display: none;">
        <div class="auth-tabs">
            <div class="auth-tab active" data-tab="login">Login</div>
            <div class="auth-tab" data-tab="register">Register</div>
        </div>
        
        <div class="auth-content">
            <!-- Login Form -->
            <form id="loginForm" class="auth-form active" action="login_process.php" method="POST">
                <div class="mb-3">
                    <input type="email" class="form-control" name="email" placeholder="Email" required>
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="rememberMe">
                    <label class="form-check-label" for="rememberMe">Remember me</label>
                </div>
                <button type="submit" class="btn btn-danger w-100">Login</button>
                <div class="text-center mt-3">
                    <a href="#" id="forgotPasswordLink">Forgot password?</a>
                </div>
            </form>
            
            <!-- Registration Form -->
            <form id="registerForm" class="auth-form" action="register_process.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <input type="text" class="form-control" name="name" placeholder="Full Name" required>
                </div>
                <div class="mb-3">
                    <input type="email" class="form-control" name="email" id="regEmail" placeholder="Email" required>
                </div>
                <div class="mb-3">
                    <input type="tel" class="form-control" name="phone" id="regPhone" placeholder="Phone Number" pattern="[0-9]{10}" title="10-digit phone number" required>
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password" required>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" name="id_number" placeholder="ID Number" required>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" name="store_number" placeholder="Store Number" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Upload Photo</label>
                    <input type="file" class="form-control" name="photo" accept="image/*" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Upload Document (PDF/DOC)</label>
                    <input type="file" class="form-control" name="document" accept=".pdf,.doc,.docx" required>
                </div>
                
                <button type="submit" class="btn btn-danger w-100">Register</button>
            </form>
            
            <!-- Forgot Password Form (hidden by default) -->
            <form id="forgotPasswordForm" class="auth-form" action="forgot_password.php" method="POST" style="display: none;">
                <div class="mb-3">
                    <input type="email" class="form-control" name="email" placeholder="Enter your email" required>
                </div>
                <button type="submit" class="btn btn-danger w-100">Reset Password</button>
                <div class="text-center mt-3">
                    <a href="#" id="backToLoginLink">Back to login</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <h2 class="text-center mb-5">Why Register With Us?</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-shop"></i>
                        </div>
                        <h3>Stall Management</h3>
                        <p>Easily manage your market stall with our intuitive tools and dashboard.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-people"></i>
                        </div>
                        <h3>Customer Reach</h3>
                        <p>Expand your customer base with our market's growing community.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h3>Secure Payments</h3>
                        <p>Safe and reliable payment processing for all your transactions.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Admin Login Container -->
   <!-- Admin Login Container -->
<div class="auth-container" id="adminAuthContainer" style="display: none;">
    <div class="auth-tabs">
        <div class="auth-tab active" data-tab="admin_login">Admin Login</div>
        <div class="auth-tab" data-tab="admin_register">Admin Register</div>
    </div>

    <div class="auth-content">
        <!-- Admin Login Form -->
        <form id="admin_loginForm" class="auth-form active" action="admin_login_process.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <input type="text" class="form-control" name="username" placeholder="Admin Username" required>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-danger w-100">Login</button>
        </form>

        <!-- Admin Register Form -->
        <form id="admin_registerForm" class="auth-form" action="admin_register_process.php" method="POST">
            <div class="mb-3">
                <input type="text" class="form-control" name="username" placeholder="Admin Username" required>
            </div>
            <div class="mb-3">
                <input type="email" class="form-control" name="email" placeholder="Admin Email" required>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password" required>
            </div>
            <div class="mb-3">
                <select class="form-control" name="role" required>
                    <option value="">Select Role</option>
                    <option value="super">Super Admin</option>
                    <option value="manager">Manager</option>
                    <option value="support">Support</option>
                </select>
            </div>
            <button type="submit" class="btn btn-danger w-100">Register Admin</button>
        </form>
    </div>
</div>
    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2025 Ongata Rongai Market Management System. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle authentication container
        document.getElementById('authToggle').addEventListener('click', function(e) {
            e.preventDefault();
            const authContainer = document.getElementById('authContainer');
            authContainer.style.display = authContainer.style.display === 'none' ? 'block' : 'none';
            // Show login form by default
            document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
            document.querySelector('.auth-tab[data-tab="login"]').classList.add('active');
            document.querySelectorAll('.auth-form').forEach(form => form.classList.remove('active'));
            document.getElementById('loginForm').classList.add('active');
            // Hide admin auth if open
            document.getElementById('adminAuthContainer').style.display = 'none';
        });

        // Show registration form directly
        document.getElementById('showRegisterForm').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('authContainer').style.display = 'block';
            // Switch to register tab
            document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
            document.querySelector('.auth-tab[data-tab="register"]').classList.add('active');
            document.querySelectorAll('.auth-form').forEach(form => form.classList.remove('active'));
            document.getElementById('registerForm').classList.add('active');
            // Scroll to the form
            document.getElementById('authContainer').scrollIntoView({ behavior: 'smooth' });
            // Hide admin auth if open
            document.getElementById('adminAuthContainer').style.display = 'none';
        });
        
        document.getElementById('joinNowBtn').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('authContainer').style.display = 'block';
            // Switch to register tab
            document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
            document.querySelector('.auth-tab[data-tab="register"]').classList.add('active');
            document.querySelectorAll('.auth-form').forEach(form => form.classList.remove('active'));
            document.getElementById('registerForm').classList.add('active');
            // Scroll to the form
            document.getElementById('authContainer').scrollIntoView({ behavior: 'smooth' });
            // Hide admin auth if open
            document.getElementById('adminAuthContainer').style.display = 'none';
        });
        
        // Tab switching
        document.querySelectorAll('.auth-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // Update tabs
                document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                // Update forms
                const tabName = this.getAttribute('data-tab');
                document.querySelectorAll('.auth-form').forEach(form => form.classList.remove('active'));
                document.getElementById(tabName + 'Form').classList.add('active');
            });
        });
        
        // Forgot password link
        document.getElementById('forgotPasswordLink').addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.auth-form').forEach(form => form.style.display = 'none');
            document.getElementById('forgotPasswordForm').style.display = 'block';
        });
        
        // Back to login link
        document.getElementById('backToLoginLink').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('forgotPasswordForm').style.display = 'none';
            document.getElementById('loginForm').style.display = 'block';
            document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
            document.querySelector('.auth-tab[data-tab="login"]').classList.add('active');
        });
        
        // Admin login toggle
        document.getElementById('adminAuthToggle').addEventListener('click', function(e) {
            e.preventDefault();
            const adminAuthContainer = document.getElementById('adminAuthContainer');
            adminAuthContainer.style.display = adminAuthContainer.style.display === 'none' ? 'block' : 'none';
            // Hide regular auth container if open
            document.getElementById('authContainer').style.display = 'none';
            // Scroll to the form
            adminAuthContainer.scrollIntoView({ behavior: 'smooth' });
        });
    </script>
<script>
    // Tab switching (works for both user and admin forms)
    document.querySelectorAll('.auth-tab').forEach(tab => {
        tab.addEventListener('click', function () {
            document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            document.querySelectorAll('.auth-form').forEach(form => form.classList.remove('active'));

            const tabName = this.getAttribute('data-tab');
            if (document.getElementById(tabName + 'Form')) {
                document.getElementById(tabName + 'Form').classList.add('active');
            }
        });
    });

    // Toggle admin register (only add event listener if element exists)
    const adminRegisterBtn = document.getElementById('adminRegisterToggle');
    if (adminRegisterBtn) {
        adminRegisterBtn.addEventListener('click', function (e) {
            e.preventDefault();
            const adminAuth = document.getElementById('adminAuthContainer');
            adminAuth.style.display = 'block';
            document.getElementById('authContainer').style.display = 'none';

            // Switch to admin register form
            document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
            document.querySelector('.auth-tab[data-tab="admin_register"]').classList.add('active');
            document.querySelectorAll('.auth-form').forEach(form => form.classList.remove('active'));
            document.getElementById('admin_registerForm').classList.add('active');

            adminAuth.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    }
</script>
</body>
</html>
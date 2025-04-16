<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Form - Market System</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
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
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('market 3.jpg') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
        }

        .form_deg {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            transition: all 0.3s ease;
            margin: 20px;
        }

        .form_deg:hover {
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            transform: translateY(-5px);
        }

        .title_deg {
            color: var(--primary-color);
            margin-bottom: 25px;
            font-size: 24px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
        }

        .title_deg::after {
            content: "";
            display: block;
            width: 60px;
            height: 4px;
            background: var(--secondary-color);
            margin: 10px auto 0;
            border-radius: 2px;
        }

        .login_form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .label_deg {
            display: block;
            margin-bottom: 8px;
            color: var(--primary-color);
            font-weight: 500;
            text-align: left;
        }

        .login_form input[type="text"],
        .login_form input[type="password"] {
            width: 100%;
            padding: 12px 15px 12px 40px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: all 0.3s;
        }

        .login_form input[type="text"]:focus,
        .login_form input[type="password"]:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
            outline: none;
        }

        .btn-primary {
            background-color: var(--secondary-color);
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            font-weight: 500;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
            color: white;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-primary:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .message {
            color: var(--secondary-color);
            margin-top: 15px;
            font-size: 14px;
            min-height: 20px;
        }

        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 38px;
            color: var(--primary-color);
            font-size: 18px;
            z-index: 10;
        }

        /* Responsive adjustments */
        @media (max-width: 480px) {
            .form_deg {
                padding: 30px 20px;
                margin: 0 15px;
            }
            
            .title_deg {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="form_deg">
        <h1 class="title_deg">Login Form</h1>
        
        <div class="message">
            <?php
            error_reporting(0);
            session_start();
            session_destroy(); 
            echo $_SESSION['loginmessage'];
            ?>
        </div>
        
        <form action="login_check.php" method="POST" class="login_form">
            <div class="input-group">
                <label class="label_deg">Username</label>
                <i class="bi bi-person-fill input-icon"></i>
                <input type="text" name="username" placeholder="Enter your username">
            </div>

            <div class="input-group">
                <label class="label_deg">Password</label>
                <i class="bi bi-lock-fill input-icon"></i>
                <input type="password" name="password" placeholder="Enter your password">
            </div>

            <div>
                <input class="btn btn-primary" type="submit" name="submit" value="Login">
            </div>
        </form>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
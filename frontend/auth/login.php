<?php
// auth/login.php
session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['user_role'];
    switch($role) {
        case 'Admin':
            header('Location: ../dashboard/admin-dashboard.php');
            break;
        case 'QuarterMaster':
            header('Location: ../dashboard/quartermaster-dashboard.php');
            break;
        case 'Department':
            header('Location: ../dashboard/department-dashboard.php');
            break;
        case 'CO':
            header('Location: ../dashboard/co-dashboard.php');
            break;
        case 'Auditor':
            header('Location: ../dashboard/auditor-dashboard.php');
            break;
        default:
            header('Location: ../dashboard/department-dashboard.php');
    }
    exit();
}

$error_message = '';
$success_message = '';

// API Configuration  
$api_base_url = 'http://localhost/students-proj/unfedZombie/Controllers/authController.php';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    // Basic validation
    if (empty($email) || empty($password)) {
        $error_message = 'Please fill in all fields.';
    } else {
        // Prepare API request
        $login_data = [
            'email' => $email,
            'password' => $password
        ];

        // Make API call to login endpoint
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_base_url . '/login');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($login_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_error) {
            $error_message = 'Connection error. Please try again later.';
            error_log("Login API Error: " . $curl_error);
        } else {
            $api_response = json_decode($response, true);
            
            if ($http_code === 200 && isset($api_response['token'])) {
                // Successful login
                $token = $api_response['token'];
                
                // Decode JWT to get user info (simple decode - in production use proper JWT library)
                $jwt_parts = explode('.', $token);
                if (count($jwt_parts) === 3) {
                    $payload = json_decode(base64_decode($jwt_parts[1]), true);
                    
                    if ($payload) {
                        // Set session variables
                        $_SESSION['user_id'] = $payload['sub'];
                        $_SESSION['username'] = $payload['email'];
                        $_SESSION['full_name'] = $payload['name'];
                        $_SESSION['user_role'] = getRoleNameFromId($payload['role_id']);
                        $_SESSION['role_id'] = $payload['role_id'];
                        $_SESSION['jwt_token'] = $token;
                        $_SESSION['token_expires'] = $payload['exp'];
                        $_SESSION['login_time'] = time();
                        
                        // Set remember me cookie if checked
                        if ($remember) {
                            $cookie_value = base64_encode($payload['sub'] . '|' . $payload['email'] . '|' . time());
                            setcookie('remember_user', $cookie_value, time() + (30 * 24 * 60 * 60), '/', '', false, true);
                        }
                        
                        // Redirect based on role
                        $role = $_SESSION['user_role'];
                        switch($role) {
                            case 'Admin':
                                header('Location: ../dashboard/admin-dashboard.php');
                                break;
                            case 'QuarterMaster':
                                header('Location: ../dashboard/quartermaster-dashboard.php');
                                break;
                            case 'Department':
                                header('Location: ../dashboard/department-dashboard.php');
                                break;
                            case 'CO':
                                header('Location: ../dashboard/co-dashboard.php');
                                break;
                            case 'Auditor':
                                header('Location: ../dashboard/auditor-dashboard.php');
                                break;
                            default:
                                header('Location: ../dashboard/department-dashboard.php');
                        }
                        exit();
                    }
                }
                
                $error_message = 'Login response format error. Please try again.';
            } else {
                // API returned error
                $error_message = isset($api_response['message']) ? $api_response['message'] : 'Invalid email or password.';
            }
        }
    }
}

// Function to convert role_id to role name - UPDATED ROLES
function getRoleNameFromId($role_id) {
    switch($role_id) {
        case 1:
            return 'Admin';
        case 2:
            return 'QuarterMaster';
        case 3:
            return 'Department';
        case 4:
            return 'CO';
        case 5:
            return 'Auditor';
        default:
            return 'Department'; // Default to Department if unknown role
    }
}

// Check for remember me cookie
$remembered_email = '';
if (isset($_COOKIE['remember_user'])) {
    try {
        $cookie_data = base64_decode($_COOKIE['remember_user']);
        $parts = explode('|', $cookie_data);
        if (count($parts) >= 2) {
            $remembered_email = $parts[1]; // Email is the second part
        }
    } catch (Exception $e) {
        // Invalid cookie, remove it
        setcookie('remember_user', '', time() - 3600, '/');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MSICT Offices Requirement Ordering System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* CSS Variables */
        :root {
            --primary-color: #2D5016;
            --secondary-color: #1e3c72;
            --accent-color: #FFD700;
            --text-color: #333;
            --light-gray: #f8f9fa;
            --white: #ffffff;
            --danger: #dc3545;
            --success: #28a745;
            --shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            --gradient: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        }

        /* Reset & Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-color);
            overflow-x: hidden;
        }

        /* Background Pattern */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle at 25% 25%, rgba(255, 215, 0, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(255, 215, 0, 0.1) 0%, transparent 50%);
            z-index: -1;
        }

        /* Login Container */
        .login-container {
            background: var(--white);
            border-radius: 20px;
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 900px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            overflow: hidden;
            margin: 20px;
            backdrop-filter: blur(10px);
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Left Panel - Logo & Info */
        .login-left {
            background: var(--gradient);
            color: var(--white);
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
        }

        .login-left::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }

        .logo-section {
            position: relative;
            z-index: 2;
        }

        .logo {
            width: 120px;
            height: 120px;
            background: var(--white);
            border-radius: 50%;
            margin: 0 auto 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: var(--primary-color);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
        }

        .system-title h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            font-weight: 700;
        }

        .system-title p {
            font-size: 1.1rem;
            opacity: 0.9;
            line-height: 1.6;
        }

        .system-subtitle {
            margin-top: 2rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            backdrop-filter: blur(10px);
        }

        /* Right Panel - Login Form */
        .login-right {
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h2 {
            color: var(--primary-color);
            font-size: 2rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .login-header p {
            color: #666;
            font-size: 1rem;
        }

        /* Alert Messages */
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alert.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Form Styles */
        .login-form {
            width: 100%;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-color);
            font-weight: 500;
            font-size: 0.95rem;
        }

        .input-wrapper {
            position: relative;
        }

        .form-control {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            border: 2px solid #e1e5e9;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: var(--light-gray);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(45, 80, 22, 0.1);
        }

        .form-control.is-invalid {
            border-color: var(--danger);
            background: #fff5f5;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            font-size: 1.1rem;
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            font-size: 1.1rem;
            padding: 0.25rem;
        }

        .password-toggle:hover {
            color: var(--primary-color);
        }

        /* Remember Me & Forgot Password */
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--primary-color);
        }

        .forgot-password {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        /* Login Button */
        .btn-login {
            width: 100%;
            padding: 1rem;
            background: var(--gradient);
            color: var(--white);
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.6s;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(45, 80, 22, 0.3);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Loading State */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: var(--white);
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Security Badge */
        .security-badge {
            text-align: center;
            margin-top: 2rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid var(--primary-color);
        }

        .security-badge i {
            color: var(--primary-color);
            margin-right: 0.5rem;
        }

        .security-badge span {
            font-size: 0.9rem;
            color: #666;
        }

        /* Footer */
        .login-footer {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e1e5e9;
            color: #666;
            font-size: 0.9rem;
        }

        /* API Status Indicator */
        .api-status {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            z-index: 1000;
        }

        .api-status.connected {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .api-status.disconnected {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .login-container {
                grid-template-columns: 1fr;
                max-width: 400px;
                margin: 10px;
            }

            .login-left {
                padding: 2rem;
            }

            .logo {
                width: 80px;
                height: 80px;
                font-size: 2rem;
            }

            .system-title h1 {
                font-size: 1.5rem;
            }

            .login-right {
                padding: 2rem;
            }

            .login-header h2 {
                font-size: 1.5rem;
            }

            .form-options {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .api-status {
                position: static;
                margin-bottom: 1rem;
                text-align: center;
            }
        }

        @media (max-width: 480px) {
            .login-container {
                margin: 5px;
            }

            .login-left,
            .login-right {
                padding: 1.5rem;
            }

            .system-title h1 {
                font-size: 1.3rem;
            }

            .system-title p {
                font-size: 1rem;
            }
        }

        /* Accessibility */
        .form-control:focus-visible,
        .btn-login:focus-visible,
        .forgot-password:focus-visible {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }

        .back-to-main {
        margin-top: 1rem;
        text-align: center;
    }
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--primary-color);
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .back-link:hover {
        background: rgba(45, 80, 22, 0.1);
        text-decoration: none;
        transform: translateX(-2px);
    }

    .back-link i {
        font-size: 0.8rem;
    }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Panel -->
        <div class="login-left">
            <div class="logo-section">
                <div class="logo">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="system-title">
                    <h1>MSICT</h1>
                    <p>Military School of Information and Communication Technology</p>
                </div>
                <div class="system-subtitle">
                    <p><i class="fas fa-clipboard-list"></i> Offices Requirement Ordering System</p>
                </div>
            </div>
        </div>

        <!-- Right Panel -->
        <div class="login-right">
        <div class="login-header">
        <h2>Secure Login</h2>
        <p>Access your account to manage office requirements</p>
        <!-- Add this back link -->
        <div class="back-to-main">
            <a href="../index.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to Main Page
            </a>
        </div>
    </div>

            <!-- Alert Messages -->
            <?php if (!empty($error_message)): ?>
                <div class="alert error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success_message)): ?>
                <div class="alert success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form class="login-form" method="POST" action="" id="loginForm">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper">
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-control <?php echo !empty($error_message) ? 'is-invalid' : ''; ?>" 
                            placeholder="Enter your email address"
                            value="<?php echo htmlspecialchars($remembered_email); ?>"
                            required
                            autocomplete="email"
                        >
                        <i class="fas fa-envelope input-icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-control <?php echo !empty($error_message) ? 'is-invalid' : ''; ?>" 
                            placeholder="Enter your password"
                            required
                            autocomplete="current-password"
                        >
                        <i class="fas fa-lock input-icon"></i>
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye" id="passwordIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="form-options">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember" <?php echo !empty($remembered_email) ? 'checked' : ''; ?>>
                        <label for="remember">Remember me</label>
                    </div>
                    <a href="#" class="forgot-password" onclick="showForgotPassword()">Forgot Password?</a>
                </div>

                <button type="submit" class="btn-login" id="loginBtn">
                    <span id="loginText">Sign In</span>
                    <div class="loading" id="loginLoader" style="display: none;"></div>
                </button>
            </form>

            <!-- Security Badge -->
            <div class="security-badge">
                <i class="fas fa-lock"></i>
                <span>Your connection is secured with JWT authentication</span>
            </div>

            <!-- Footer -->
            <div class="login-footer">
                <p>&copy; <?php echo date('Y'); ?> MSICT. All rights reserved. | Version 1.0</p>
                <p style="margin-top: 0.5rem; font-size: 0.8rem;">Powered by Secure API Authentication</p>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        }

        // Forgot password handler
        function showForgotPassword() {
            alert('Please contact your system administrator to reset your password.\n\nContact Information:\nEmail: support@msict.mil.tz\nPhone: +255-XX-XXX-XXXX');
        }

        // Form submission with loading state and client-side validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const loginBtn = document.getElementById('loginBtn');
            const loginText = document.getElementById('loginText');
            const loginLoader = document.getElementById('loginLoader');

            // Basic client-side validation
            if (!email || !password) {
                e.preventDefault();
                alert('Please fill in all fields.');
                return;
            }

            if (!isValidEmail(email)) {
                e.preventDefault();
                alert('Please enter a valid email address.');
                return;
            }

            // Show loading state
            loginBtn.disabled = true;
            loginText.style.display = 'none';
            loginLoader.style.display = 'inline-block';
        });

        // Email validation function
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        // Test API connectivity on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Focus on email field
            document.getElementById('email').focus();
            
            // Test API connectivity (optional)
            testAPIConnectivity();
        });

        // Handle enter key in form fields
        document.getElementById('email').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('password').focus();
            }
        });

        // Test API connectivity
        function testAPIConnectivity() {
            fetch('<?php echo $api_base_url; ?>/test', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (response.ok) {
                    console.log('API connectivity test passed');
                } else {
                    console.warn('API might not be available');
                }
            })
            .catch(error => {
                console.warn('API connectivity test failed:', error);
            });
        }

        // Auto-clear error messages after 10 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 500);
            });
        }, 10000);
    </script>
</body>
</html>
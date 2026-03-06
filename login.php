<?php
require_once 'includes/auth.php';
require_once 'includes/helpers.php';

if (Auth::isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'login') {
        $username = sanitizeInput($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if ($username && $password) {
            if ($auth->login($username, $password)) {
                redirect('dashboard.php');
            } else {
                $error = 'Invalid username or password';
            }
        } else {
            $error = 'Please fill in all fields';
        }
    } 
    elseif ($action === 'register') {
        $username = sanitizeInput($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $fullname = sanitizeInput($_POST['fullname'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $phone = sanitizeInput($_POST['phone'] ?? '');
        $role = $_POST['role'] ?? 'student';
        
        if (!$username || !$password || !$fullname || !$email) {
            $error = 'Please fill in all required fields';
        } elseif ($password !== $confirmPassword) {
            $error = 'Passwords do not match';
        } elseif (!validateEmail($email)) {
            $error = 'Invalid email address';
        } else {
            $result = $auth->register($username, $password, $fullname, $email, $phone, $role);
            if ($result['success']) {
                $success = 'Registration successful! Please login.';
            } else {
                $error = $result['message'];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Student Management System</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h1>Student Management System</h1>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <div class="auth-tabs">
                <button class="tab-btn active login-tab-btn" onclick="showTab('login')">Login</button>
                <button class="tab-btn register-tab-btn" onclick="showTab('register')">Register</button>
            </div>
            
            <!-- Login Tab -->
            <div id="login" class="tab-content active">
                <form method="POST" class="form">
                    <input type="hidden" name="action" value="login">
                    
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">Login</button>
                </form>
                
                <p class="auth-note">Demo Credentials:</p>
                <ul style="margin: 10px 0; font-size: 12px;">
                    <li>Teacher: teacher / password123</li>
                    <li>Student: student / password123</li>
                </ul>
            </div>
            
            <!-- Register Tab -->
            <div id="register" class="tab-content">
                <form method="POST" class="form">
                    <input type="hidden" name="action" value="register">
                    
                    <div class="form-group">
                        <label for="reg-username">Username:</label>
                        <input type="text" id="reg-username" name="username" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="reg-email">Email:</label>
                        <input type="email" id="reg-email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="reg-fullname">Full Name:</label>
                        <input type="text" id="reg-fullname" name="fullname" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="reg-phone">Phone (optional):</label>
                        <input type="tel" id="reg-phone" name="phone">
                    </div>
                    
                    <div class="form-group">
                        <label for="reg-password">Password:</label>
                        <input type="password" id="reg-password" name="password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="reg-confirm">Confirm Password:</label>
                        <input type="password" id="reg-confirm" name="confirm_password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="reg-role">Account Type:</label>
                        <select id="reg-role" name="role" required>
                            <option value="student">Student</option>
                            <option value="teacher">Teacher</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">Register</button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        function showTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Remove active class from all buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Show selected tab
            document.getElementById(tabName).classList.add('active');
            
            // Add active class to clicked button
            if (tabName === 'login') {
                document.querySelector('.login-tab-btn').classList.add('active');
            } else {
                document.querySelector('.register-tab-btn').classList.add('active');
            }
        }
    </script>
</body>
</html>

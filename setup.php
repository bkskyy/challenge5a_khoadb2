<?php
/**
 * SETUP SCRIPT - Run this once to add demo data
 * 
 * This script creates demo users (teacher and student) in your database
 * 
 * HOW TO USE:
 * 1. Upload this file to your server
 * 2. Visit: http://localhost/PROG05/setup.php
 * 3. Follow instructions and click "Create Demo Data" button
 * 4. Delete this file after setup is complete (for security)
 */

require_once 'includes/db.php';
require_once 'includes/auth.php';

$setup_complete = false;
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = Database::getInstance();
        $auth = new Auth();
        
        // Check if demo users already exist
        $sql = "SELECT id FROM users WHERE username IN ('teacher', 'student')";
        $existing = $db->fetchAll($sql);
        
        if (!empty($existing)) {
            $error = 'Demo users already exist! If you want to reset, delete users manually from database.';
        } else {
            // Create teacher account
            $result1 = $auth->register(
                'teacher',
                'password123',
                'Mr. Teacher',
                'teacher@example.com',
                '555-0001',
                'teacher'
            );
            
            // Create student account
            $result2 = $auth->register(
                'student',
                'password123',
                'John Student',
                'student@example.com',
                '555-0002',
                'student'
            );
            
            if ($result1['success'] && $result2['success']) {
                $setup_complete = true;
                $message = 'Demo data created successfully!<br>' .
                          '<strong>Teacher:</strong> username: teacher, password: password123<br>' .
                          '<strong>Student:</strong> username: student, password: password123<br>' .
                          '<br>You can now <a href="login.php">login here</a>';
            } else {
                $error = 'Error creating demo data: ' . ($result1['success'] ? $result2['message'] : $result1['message']);
            }
        }
    } catch (Exception $e) {
        $error = 'Setup Error: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup - Student Management System</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h1>🔧 Setup</h1>
            <p style="text-align: center; color: #666; margin-bottom: 20px;">
                Student Management System Installation
            </p>
            
            <?php if ($setup_complete): ?>
                <div class="alert alert-success">
                    <?php echo $message; ?>
                </div>
            <?php elseif ($error): ?>
                <div class="alert alert-error">
                    <?php echo $error; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <strong>Before Setup:</strong><br>
                    1. Run the database.sql file in your MySQL<br>
                    2. Configure db.php credentials<br>
                    3. Ensure upload directories exist<br>
                    <br>
                    Then click the button below to create demo user accounts.
                </div>
            <?php endif; ?>
            
            <?php if (!$setup_complete): ?>
                <form method="POST" class="form">
                    <button type="submit" class="btn btn-primary btn-block">
                        Create Demo Data
                    </button>
                </form>
                
                <hr style="margin: 20px 0; border: none; border-top: 1px solid #ddd;">
                
                <h3>Setup Instructions</h3>
                <ol style="text-align: left; color: #666; padding-left: 20px;">
                    <li>Import database.sql into MySQL</li>
                    <li>Update includes/db.php with your database credentials</li>
                    <li>Create these directories with 755 permissions:
                        <ul>
                            <li>uploads/avatars/</li>
                            <li>uploads/assignments/</li>
                            <li>uploads/puzzles/</li>
                        </ul>
                    </li>
                    <li>Click "Create Demo Data" above</li>
                    <li>Delete this setup.php file for security</li>
                    <li>Visit login.php to start using the system</li>
                </ol>
            <?php endif; ?>
            
            <div style="margin-top: 30px; text-align: center;">
                <a href="login.php" class="btn btn-primary" style="display: inline-block;">Go to Login</a>
            </div>
        </div>
    </div>
</body>
</html>

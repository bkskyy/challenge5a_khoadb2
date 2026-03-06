<?php
require_once 'includes/auth.php';
require_once 'includes/helpers.php';

requireLogin();

$user = Auth::getCurrentUser();
$db = Database::getInstance();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Student Management System</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <?php $root_path = ''; $page_title = 'Dashboard'; include 'includes/header.php'; ?>
    
    <div class="dashboard-container">
        <h1>Welcome, <?php echo htmlspecialchars($user['fullname']); ?>!</h1>
        <p>Role: <strong><?php echo ucfirst($user['role']); ?></strong></p>
        
        <?php if (Auth::hasRole('teacher')): ?>
            <div class="dashboard-grid">
                <div class="dashboard-card">
                    <h3>📋 Manage Students</h3>
                    <p>Add, edit, and delete student accounts</p>
                    <a href="teacher/manage_students.php" class="btn btn-primary">Go to Students</a>
                </div>
                
                <div class="dashboard-card">
                    <h3>📎 Assignments</h3>
                    <p>Upload files for students to download</p>
                    <a href="teacher/assignments.php" class="btn btn-primary">Manage Assignments</a>
                </div>
                
                <div class="dashboard-card">
                    <h3>🧩 Puzzles</h3>
                    <p>Create puzzle games for students</p>
                    <a href="teacher/puzzles.php" class="btn btn-primary">Manage Puzzles</a>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (Auth::hasRole('student')): ?>
            <div class="dashboard-grid">
                <div class="dashboard-card">
                    <h3>📎 Assignments</h3>
                    <p>View and download assignments from teachers</p>
                    <a href="student/assignments.php" class="btn btn-primary">View Assignments</a>
                </div>
                
                <div class="dashboard-card">
                    <h3>🧩 Puzzles</h3>
                    <p>Solve puzzle games created by teachers</p>
                    <a href="student/puzzles.php" class="btn btn-primary">Solve Puzzles</a>
                </div>
                
                <div class="dashboard-card">
                    <h3>👤 My Profile</h3>
                    <p>Update your profile information</p>
                    <a href="profile.php" class="btn btn-primary">Edit Profile</a>
                </div>
            </div>
        <?php endif; ?>
        
        <div style="margin-top: 30px;">
            <div class="dashboard-card">
                <h3>💬 Messages</h3>
                <p>Send and receive messages</p>
                <a href="messages.php" class="btn btn-primary">Go to Messages</a>
            </div>
            
            <div class="dashboard-card">
                <h3>👥 Users</h3>
                <p>View all users in the system</p>
                <a href="users.php" class="btn btn-primary">View Users</a>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>

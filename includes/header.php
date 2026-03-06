<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/helpers.php';

if (!Auth::isLoggedIn()) {
    redirect('login.php');
}

$user = Auth::getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - Student Management System' : 'Student Management System'; ?></title>
    <link rel="stylesheet" href="<?php echo (isset($root_path) ? $root_path : ''); ?>assets/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <a href="<?php echo (isset($root_path) ? $root_path : ''); ?>dashboard.php">Student Management System</a>
            </div>
            <ul class="nav-menu">
                <li><a href="<?php echo (isset($root_path) ? $root_path : ''); ?>dashboard.php">Dashboard</a></li>
                
                <?php if (Auth::hasRole('teacher')): ?>
                    <li><a href="<?php echo (isset($root_path) ? $root_path : ''); ?>teacher/manage_students.php">Manage Students</a></li>
                    <li><a href="<?php echo (isset($root_path) ? $root_path : ''); ?>teacher/assignments.php">Assignments</a></li>
                    <li><a href="<?php echo (isset($root_path) ? $root_path : ''); ?>teacher/puzzles.php">Puzzles</a></li>
                <?php endif; ?>
                
                <?php if (Auth::hasRole('student')): ?>
                    <li><a href="<?php echo (isset($root_path) ? $root_path : ''); ?>student/assignments.php">Assignments</a></li>
                    <li><a href="<?php echo (isset($root_path) ? $root_path : ''); ?>student/puzzles.php">Puzzles</a></li>
                <?php endif; ?>
                
                <li><a href="<?php echo (isset($root_path) ? $root_path : ''); ?>messages.php">Messages</a></li>
                <li><a href="<?php echo (isset($root_path) ? $root_path : ''); ?>users.php">Users</a></li>
                <li><a href="<?php echo (isset($root_path) ? $root_path : ''); ?>profile.php">Profile</a></li>
                <li><a href="<?php echo (isset($root_path) ? $root_path : ''); ?>logout.php" class="logout-btn">Logout</a></li>
            </ul>
        </div>
    </nav>
    <div class="container">

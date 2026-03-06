<?php
require_once 'includes/auth.php';
require_once 'includes/helpers.php';

requireLogin();

$db = Database::getInstance();
$currentUser = Auth::getCurrentUser();

// Get all users except current user
$sql = "SELECT id, username, fullname, email, avatar, role FROM users WHERE id != ? ORDER BY fullname ASC";
$users = $db->fetchAll($sql, [$currentUser['id']]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - Student Management System</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <?php $root_path = ''; $page_title = 'Users'; include 'includes/header.php'; ?>
    
    <h1>All Users</h1>
    
    <div class="users-grid">
        <?php if ($users): ?>
            <?php foreach ($users as $otherUser): ?>
                <div class="user-card">
                    <div class="user-avatar">
                        <?php if ($otherUser['avatar'] && file_exists($otherUser['avatar'])): ?>
                            <img src="<?php echo htmlspecialchars($otherUser['avatar']); ?>" alt="<?php echo htmlspecialchars($otherUser['fullname']); ?>">
                        <?php else: ?>
                            <div class="avatar-placeholder-small">👤</div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="user-info">
                        <h3><?php echo htmlspecialchars($otherUser['fullname']); ?></h3>
                        <p><strong>@<?php echo htmlspecialchars($otherUser['username']); ?></strong></p>
                        <p class="role-badge"><?php echo ucfirst($otherUser['role']); ?></p>
                        <?php if ($otherUser['email']): ?>
                            <p>📧 <?php echo htmlspecialchars($otherUser['email']); ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="user-actions">
                        <a href="messages.php?user_id=<?php echo $otherUser['id']; ?>" class="btn btn-small btn-primary">Send Message</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No users found</p>
        <?php endif; ?>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>

<?php
require_once 'includes/auth.php';
require_once 'includes/helpers.php';

requireLogin();

$db = Database::getInstance();
$user = Auth::getCurrentUser();
$userId = Auth::getCurrentUserId();
$error = '';
$success = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    
    // Validate email
    if (!validateEmail($email)) {
        $error = 'Invalid email address';
    } else {
        // Check if email is already taken by another user
        $sql = "SELECT id FROM users WHERE email = ? AND id != ?";
        if ($db->fetchOne($sql, [$email, $userId])) {
            $error = 'Email already taken';
        } else {
            // Update email and phone
            $sql = "UPDATE users SET email = ?, phone = ? WHERE id = ?";
            $db->update($sql, [$email, $phone, $userId]);
            
            // Update session
            $_SESSION['email'] = $email;
            
            $success = 'Profile updated successfully';
        }
    }
}

// Handle avatar changes
$avatarError = '';
$avatarSuccess = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['avatar_action'])) {
    $action = $_POST['avatar_action'];
    $uploadDir = 'uploads/avatars';
    
    if ($action === 'upload' && isset($_FILES['avatar'])) {
        $result = uploadFile($_FILES['avatar'], $uploadDir, ['jpg', 'jpeg', 'png', 'gif']);
        
        if ($result['success']) {
            // Delete old avatar if exists
            if ($user['avatar'] && file_exists($user['avatar'])) {
                unlink($user['avatar']);
            }
            
            // Update database
            $sql = "UPDATE users SET avatar = ? WHERE id = ?";
            $db->update($sql, [$result['path'], $userId]);
            
            // Update session
            $_SESSION['avatar'] = $result['path'];
            
            $avatarSuccess = 'Avatar uploaded successfully';
            $user = Auth::getCurrentUser();
        } else {
            $avatarError = $result['message'];
        }
    }
    elseif ($action === 'url' && !empty($_POST['avatar_url'])) {
        $url = $_POST['avatar_url'];
        $filename = uniqid() . '_' . time() . '.jpg';
        $filePath = $uploadDir . '/' . $filename;
        
        // Ensure directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $result = downloadFileFromUrl($url, $filePath);
        
        if ($result['success']) {
            // Delete old avatar if exists
            if ($user['avatar'] && file_exists($user['avatar'])) {
                unlink($user['avatar']);
            }
            
            // Update database
            $sql = "UPDATE users SET avatar = ? WHERE id = ?";
            $db->update($sql, [$filePath, $userId]);
            
            // Update session
            $_SESSION['avatar'] = $filePath;
            
            $avatarSuccess = 'Avatar updated from URL successfully';
            $user = Auth::getCurrentUser();
        } else {
            $avatarError = $result['message'];
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Student Management System</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <?php $root_path = ''; $page_title = 'My Profile'; include 'includes/header.php'; ?>
    
    <div class="profile-container">
        <h1>My Profile</h1>
        
        <div class="profile-grid">
            <!-- Profile Info -->
            <div class="profile-section">
                <h2>Account Information</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <form method="POST" class="form">
                    <div class="form-group">
                        <label>Username:</label>
                        <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                        <small>Cannot be changed</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Full Name:</label>
                        <input type="text" value="<?php echo htmlspecialchars($user['fullname']); ?>" disabled>
                        <small>Cannot be changed</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone:</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
            
            <!-- Avatar Section -->
            <div class="profile-section">
                <h2>Profile Avatar</h2>
                
                <?php if ($avatarError): ?>
                    <div class="alert alert-error"><?php echo $avatarError; ?></div>
                <?php endif; ?>
                
                <?php if ($avatarSuccess): ?>
                    <div class="alert alert-success"><?php echo $avatarSuccess; ?></div>
                <?php endif; ?>
                
                <div class="avatar-display">
                    <?php if ($user['avatar'] && file_exists($user['avatar'])): ?>
                        <img src="<?php echo htmlspecialchars($user['avatar']); ?>" alt="Avatar" class="avatar-img">
                    <?php else: ?>
                        <div class="avatar-placeholder">No Avatar</div>
                    <?php endif; ?>
                </div>
                
                <!-- Upload Avatar -->
                <form method="POST" enctype="multipart/form-data" class="form" style="margin-top: 20px;">
                    <input type="hidden" name="avatar_action" value="upload">
                    <div class="form-group">
                        <label for="avatar">Upload Avatar (JPG, PNG, GIF):</label>
                        <input type="file" id="avatar" name="avatar" accept="image/*" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Upload Avatar</button>
                </form>
                
                <!-- Avatar from URL -->
                <form method="POST" class="form" style="margin-top: 20px;">
                    <input type="hidden" name="avatar_action" value="url">
                    <div class="form-group">
                        <label for="avatar_url">Or use Image URL:</label>
                        <input type="url" id="avatar_url" name="avatar_url" placeholder="https://example.com/image.jpg">
                    </div>
                    <button type="submit" class="btn btn-primary">Set Avatar from URL</button>
                </form>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>

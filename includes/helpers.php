<?php
/**
 * Helper Functions
 */

require_once __DIR__ . '/db.php';

/**
 * Upload file and return file path
 */
function uploadFile($file, $uploadDir, $allowedExtensions = []) {
    // Check if file was uploaded without errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File upload failed'];
    }
    
    // Get file extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // Check allowed extensions
    if (!empty($allowedExtensions) && !in_array($extension, $allowedExtensions)) {
        return ['success' => false, 'message' => 'File type not allowed'];
    }
    
    // Create unique filename
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $filePath = $uploadDir . '/' . $filename;
    
    // Ensure upload directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        return ['success' => true, 'filename' => $filename, 'path' => $filePath];
    }
    
    return ['success' => false, 'message' => 'Failed to move uploaded file'];
}

/**
 * Download file from URL and save locally
 */
function downloadFileFromUrl($url, $savePath) {
    try {
        // Validate URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return ['success' => false, 'message' => 'Invalid URL'];
        }
        
        // Download file
        $fileContent = file_get_contents($url);
        if ($fileContent === false) {
            return ['success' => false, 'message' => 'Failed to download file'];
        }
        
        // Ensure directory exists
        $uploadDir = dirname($savePath);
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Save file
        if (file_put_contents($savePath, $fileContent) !== false) {
            return ['success' => true, 'path' => $savePath];
        }
        
        return ['success' => false, 'message' => 'Failed to save file'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Get file extension
 */
function getFileExtension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

/**
 * Get filename without extension
 */
function getFilenameWithoutExtension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_FILENAME));
}

/**
 * Check if file exists
 */
function fileExists($filePath) {
    return file_exists($filePath);
}

/**
 * Delete file
 */
function deleteFile($filePath) {
    if (file_exists($filePath)) {
        return unlink($filePath);
    }
    return false;
}

/**
 * Sanitize input
 */
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Generate random token
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Format date
 */
function formatDate($date, $format = 'd M Y H:i') {
    $timestamp = strtotime($date);
    return date($format, $timestamp);
}

/**
 * Get time ago
 */
function timeAgo($datetime) {
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;
    
    if ($diff < 60) {
        return 'just now';
    } elseif ($diff < 3600) {
        $minutes = round($diff / 60);
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = round($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = round($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return date('d M Y', $time);
    }
}

/**
 * Redirect to page
 */
function redirect($page) {
    header('Location: ' . $page);
    exit();
}

/**
 * Get user by ID
 */
function getUserById($userId) {
    $db = Database::getInstance();
    $sql = "SELECT id, username, fullname, email, phone, avatar, role FROM users WHERE id = ?";
    return $db->fetchOne($sql, [$userId]);
}

/**
 * Get all users except current user
 */
function getAllUsers($excludeId = null) {
    $db = Database::getInstance();
    $sql = "SELECT id, username, fullname, email, avatar, role FROM users";
    $params = [];
    
    if ($excludeId) {
        $sql .= " WHERE id != ?";
        $params[] = $excludeId;
    }
    
    $sql .= " ORDER BY fullname ASC";
    return $db->fetchAll($sql, $params);
}

/**
 * Count unread messages
 */
function getUnreadMessageCount($userId) {
    $db = Database::getInstance();
    $sql = "SELECT COUNT(*) as count FROM messages WHERE receiver_id = ? AND is_read = 0";
    $result = $db->fetchOne($sql, [$userId]);
    return $result['count'] ?? 0;
}
?>

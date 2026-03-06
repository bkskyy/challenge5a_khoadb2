<?php
/**
 * Authentication System
 * Handles login, logout, and session management
 */

session_start();

require_once __DIR__ . '/db.php';

class Auth {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Login user with username and password
     */
    public function login($username, $password) {
        $sql = "SELECT * FROM users WHERE username = ?";
        $user = $this->db->fetchOne($sql, [$username]);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['avatar'] = $user['avatar'];
            return true;
        }
        return false;
    }
    
    /**
     * Register new user
     */
    public function register($username, $password, $fullname, $email, $phone, $role = 'student') {
        try {
            // Check if username already exists
            $sql = "SELECT id FROM users WHERE username = ?";
            if ($this->db->fetchOne($sql, [$username])) {
                return ['success' => false, 'message' => 'Username already exists'];
            }
            
            // Check if email already exists
            $sql = "SELECT id FROM users WHERE email = ?";
            if ($this->db->fetchOne($sql, [$email])) {
                return ['success' => false, 'message' => 'Email already exists'];
            }
            
            // Insert new user
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $sql = "INSERT INTO users (username, password, fullname, email, phone, role) VALUES (?, ?, ?, ?, ?, ?)";
            $userId = $this->db->insert($sql, [$username, $hashedPassword, $fullname, $email, $phone, $role]);
            
            return ['success' => true, 'message' => 'User registered successfully', 'user_id' => $userId];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Registration error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Logout user
     */
    public function logout() {
        session_unset();
        session_destroy();
    }
    
    /**
     * Check if user is logged in
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Check if user has specific role
     */
    public static function hasRole($role) {
        return isset($_SESSION['role']) && $_SESSION['role'] === $role;
    }
    
    /**
     * Get current user ID
     */
    public static function getCurrentUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Get current user info
     */
    public static function getCurrentUser() {
        if (!self::isLoggedIn()) {
            return null;
        }
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'fullname' => $_SESSION['fullname'],
            'role' => $_SESSION['role'],
            'email' => $_SESSION['email'],
            'avatar' => $_SESSION['avatar']
        ];
    }
}

$auth = new Auth();

/**
 * Require login - redirect if not logged in
 */
function requireLogin() {
    if (!Auth::isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

/**
 * Require teacher role
 */
function requireTeacher() {
    requireLogin();
    if (!Auth::hasRole('teacher')) {
        die('Access denied: Teacher role required');
    }
}

/**
 * Require student role
 */
function requireStudent() {
    requireLogin();
    if (!Auth::hasRole('student')) {
        die('Access denied: Student role required');
    }
}
?>

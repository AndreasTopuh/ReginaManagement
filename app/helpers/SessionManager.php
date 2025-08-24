<?php
/**
 * Session Management Class
 */
class SessionManager {
    
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            // Security settings
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
            
            session_start();
        }
    }
    
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            // Store intended URL
            $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
            redirect('/login.php');
            exit;
        }
        
        // Check session timeout (24 hours)
        if (isset($_SESSION['last_activity'])) {
            $timeout = 24 * 60 * 60; // 24 hours
            if (time() - $_SESSION['last_activity'] > $timeout) {
                self::destroy();
                setFlashMessage('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
                redirect('/login.php');
                exit;
            }
        }
        
        $_SESSION['last_activity'] = time();
    }
    
    public static function requireRole($required_roles) {
        self::requireLogin();
        
        if (!is_array($required_roles)) {
            $required_roles = [$required_roles];
        }
        
        $user_role = $_SESSION['role_name'] ?? '';
        
        if (!in_array($user_role, $required_roles)) {
            setFlashMessage('error', 'Anda tidak memiliki akses ke halaman ini.');
            redirect('/dashboard.php');
            exit;
        }
    }
    
    public static function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    
    public static function getUsername() {
        return $_SESSION['username'] ?? null;
    }
    
    public static function getFullName() {
        return $_SESSION['name'] ?? null;
    }
    
    public static function getUserRole() {
        return $_SESSION['role_name'] ?? null;
    }
    
    public static function getRoleId() {
        return $_SESSION['role_id'] ?? null;
    }
    
    public static function destroy() {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }
    
    public static function regenerateId() {
        session_regenerate_id(true);
    }
}

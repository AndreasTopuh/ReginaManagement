<?php
class AuthController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function login()
    {
        // Redirect if already logged in
        if (isset($_SESSION['user_id'])) {
            redirect('/dashboard.php');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleLoginSubmit();
        }

        return $this->showLoginForm();
    }

    private function handleLoginSubmit()
    {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        // Validation
        if (empty($username) || empty($password)) {
            setFlashMessage('error', 'Username dan password harus diisi.');
            return $this->showLoginForm();
        }

        // Rate limiting (simple)
        if (!$this->checkRateLimit()) {
            setFlashMessage('error', 'Terlalu banyak percobaan login. Silakan coba lagi dalam beberapa menit.');
            return $this->showLoginForm();
        }

        try {
            $user = $this->userModel->authenticate($username, $password);

            if ($user) {
                // Check if user is active
                if ($user['status'] != 1) {
                    setFlashMessage('error', 'Akun Anda tidak aktif. Silakan hubungi administrator.');
                    return $this->showLoginForm();
                }

                // Create session
                $this->createUserSession($user);

                // Log login activity
                $this->logLoginActivity($user['id'], true);

                // Redirect based on role or intended page
                $redirect_url = $_SESSION['intended_url'] ?? '/dashboard.php';
                unset($_SESSION['intended_url']);

                redirect($redirect_url);
            } else {
                // Log failed login attempt
                $this->logLoginActivity(null, false, $username);

                setFlashMessage('error', 'Username atau password salah.');
                return $this->showLoginForm();
            }
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            setFlashMessage('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
            return $this->showLoginForm();
        }
    }

    private function createUserSession($user)
    {
        // Regenerate session ID for security
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role_id'] = $user['role_id'];
        $_SESSION['role_name'] = $user['role_name'];
        $_SESSION['last_activity'] = time();
        $_SESSION['login_time'] = time();
    }

    private function checkRateLimit()
    {
        $key = 'login_attempts_' . $_SERVER['REMOTE_ADDR'];
        $attempts = $_SESSION[$key] ?? 0;
        $last_attempt = $_SESSION[$key . '_time'] ?? 0;

        // Reset counter if more than 15 minutes passed
        if (time() - $last_attempt > 900) {
            unset($_SESSION[$key]);
            unset($_SESSION[$key . '_time']);
            return true;
        }

        // Allow maximum 5 attempts
        return $attempts < 5;
    }

    private function logLoginActivity($user_id, $success, $username = null)
    {
        try {
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

            // Log to database if you have login_logs table
            // For now, just log to file
            $log_message = sprintf(
                "[%s] Login %s - User: %s, IP: %s, UA: %s",
                date('Y-m-d H:i:s'),
                $success ? 'SUCCESS' : 'FAILED',
                $username ?? ($user_id ? "ID:$user_id" : 'unknown'),
                $ip_address,
                substr($user_agent, 0, 100)
            );

            error_log($log_message);
        } catch (Exception $e) {
            error_log("Failed to log login activity: " . $e->getMessage());
        }
    }

    public function logout()
    {
        $user_id = $_SESSION['user_id'] ?? null;

        // Log logout activity
        if ($user_id) {
            error_log("[" . date('Y-m-d H:i:s') . "] Logout - User ID: $user_id");
        }

        // Destroy session
        $_SESSION = [];
        session_destroy();

        // Remove session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        setFlashMessage('success', 'Anda telah berhasil logout.');
        redirect('/login.php');
    }

    private function showLoginForm()
    {
        include APP_PATH . '/views/auth/login.php';
    }
}

<?php

class BaseController {
    protected $session;
    
    public function __construct() {
        $this->session = new SessionManager();
    }
    
    protected function render($view, $data = []) {
        // Extract data for view
        extract($data);
        
        // Build view path
        $viewPath = APP_PATH . '/views/' . str_replace('.', '/', $view) . '.php';
        
        if (!file_exists($viewPath)) {
            throw new Exception("View $view not found");
        }
        
        // Include the view
        include $viewPath;
    }
    
    protected function redirect($url, $statusCode = 302) {
        // Ensure URL starts with /
        if (strpos($url, '/') !== 0) {
            $url = '/' . $url;
        }
        
        http_response_code($statusCode);
        header('Location: ' . BASE_URL . $url);
        exit;
    }
    
    protected function requireLogin() {
        SessionManager::requireLogin();
    }
    
    protected function requireRole($roles) {
        SessionManager::requireRole($roles);
    }
    
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    protected function back($fallback = '/') {
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if ($referer && strpos($referer, $_SERVER['HTTP_HOST']) !== false) {
            header('Location: ' . $referer);
        } else {
            $this->redirect($fallback);
        }
        exit;
    }
    
    protected function flashMessage($type, $message) {
        setFlashMessage($type, $message);
    }
    
    protected function getFlashMessage() {
        return getFlashMessage();
    }
    
    protected function validate($rules, $data) {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;
            $ruleArray = is_string($rule) ? explode('|', $rule) : $rule;
            
            foreach ($ruleArray as $singleRule) {
                if ($singleRule === 'required' && empty($value)) {
                    $errors[$field] = ucfirst($field) . ' is required';
                    break;
                }
                
                if (strpos($singleRule, 'min:') === 0 && strlen($value) < (int)substr($singleRule, 4)) {
                    $errors[$field] = ucfirst($field) . ' must be at least ' . substr($singleRule, 4) . ' characters';
                    break;
                }
                
                if (strpos($singleRule, 'max:') === 0 && strlen($value) > (int)substr($singleRule, 4)) {
                    $errors[$field] = ucfirst($field) . ' must not exceed ' . substr($singleRule, 4) . ' characters';
                    break;
                }
                
                if ($singleRule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = ucfirst($field) . ' must be a valid email address';
                    break;
                }
            }
        }
        
        return $errors;
    }
}

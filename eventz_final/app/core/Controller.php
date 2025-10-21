<?php
/**
 * Base Controller Class
 * All controllers extend this class
 */

class Controller {
    protected $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Load a view file
     */
    protected function view($view, $data = []) {
        extract($data);
        
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("View not found: " . $view);
        }
    }
    
    /**
     * Load a model
     */
    protected function model($model) {
        $modelFile = __DIR__ . '/../models/' . $model . '.php';
        
        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $model();
        } else {
            die("Model not found: " . $model);
        }
    }
    
    /**
     * Redirect to another page
     */
    protected function redirect($path) {
        header('Location: ' . FULL_URL . $path);
        exit;
    }
    
    /**
     * Return JSON response
     */
    protected function json($data, $statusCode = 200) {
        // Clear any output buffer to prevent HTML from mixing with JSON
        if (ob_get_level()) {
            ob_clean();
        }
        
        // Disable error display for this response
        ini_set('display_errors', 0);
        
        http_response_code($statusCode);
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, must-revalidate');
        
        echo json_encode($data);
        exit;
    }
    
    /**
     * Check if user is logged in
     */
    protected function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Get current user ID
     */
    protected function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Get current user role
     */
    protected function getUserRole() {
        return $_SESSION['user_role'] ?? null;
    }
    
    /**
     * Require authentication
     */
    protected function requireAuth() {
        if (!$this->isLoggedIn()) {
            $this->redirect('/login');
        }
    }
    
    /**
     * Require specific role
     */
    protected function requireRole($role) {
        $this->requireAuth();
        
        if ($this->getUserRole() !== $role) {
            $this->redirect('/');
        }
    }
    
    /**
     * Get POST data
     */
    protected function post($key, $default = null) {
        return $_POST[$key] ?? $default;
    }
    
    /**
     * Get GET data
     */
    protected function get($key, $default = null) {
        return $_GET[$key] ?? $default;
    }
    
    /**
     * Sanitize input
     */
    protected function sanitize($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Validate email
     */
    protected function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    
    /**
     * Upload file
     */
    protected function uploadFile($file, $destination, $allowedTypes, $maxSize) {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'No file uploaded or upload error'];
        }
        
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'message' => 'File size exceeds limit'];
        }
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $allowedTypes)) {
            return ['success' => false, 'message' => 'Invalid file type'];
        }
        
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $filepath = $destination . $filename;
        
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['success' => true, 'filename' => $filename, 'path' => $filepath];
        }
        
        return ['success' => false, 'message' => 'Failed to move uploaded file'];
    }
}
<?php
/**
 * Auth Controller
 * Handles authentication (login, register, logout)
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Category.php';

class AuthController extends Controller {
    private $userModel;
    private $categoryModel;
    
    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
        $this->categoryModel = new Category();
    }
    
    /**
     * Show login form
     */
    public function showLogin() {
        if ($this->isLoggedIn()) {
            // Redirect based on user role
            $role = $this->getUserRole();
            switch ($role) {
                case 'admin':
                    $this->redirect('/admin/dashboard');
                    break;
                case 'organizer':
                    $this->redirect('/organizer/dashboard');
                    break;
                case 'sponsor':
                    $this->redirect('/sponsor/dashboard');
                    break;
                case 'supplier':
                    $this->redirect('/supplier/dashboard');
                    break;
                default:
                    $this->redirect('/participant/home');
            }
        }
        
        $this->view('auth/login');
    }
    
    /**
     * Handle login
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
        }
        
        $email = $this->sanitize($this->post('email'));
        $password = $this->post('password');
        
        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Please fill in all fields';
            $this->redirect('/login');
        }
        
        $user = $this->userModel->findByEmail($email);
        
        if (!$user || !password_verify($password, $user['password_hash'])) {
            $_SESSION['error'] = 'Invalid email or password';
            $this->redirect('/login');
        }
        
        // Set session variables
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_avatar'] = $user['avatar_url'];
        
        // Get user roles and choose a supported primary role (no supplier dashboard)
        $roles = $this->userModel->getRoles($user['id']);
        $_SESSION['user_roles'] = $roles;
        $priority = ['admin', 'organizer', 'sponsor', 'participant'];
        $primaryRole = 'participant';
        foreach ($priority as $r) {
            if (in_array($r, $roles, true)) { $primaryRole = $r; break; }
        }
        $_SESSION['user_role'] = $primaryRole;
        
        // Update last login
        $this->userModel->updateLastLogin($user['id']);
        
        // Redirect based on role
        $role = $_SESSION['user_role'];
        switch ($role) {
            case 'admin':
                $this->redirect('/admin/dashboard');
                break;
            case 'organizer':
                $this->redirect('/organizer/dashboard');
                break;
            case 'sponsor':
                $this->redirect('/sponsor/dashboard');
                break;
            default:
                $this->redirect('/participant/home');
        }
    }
    
    /**
     * Show registration form
     */
    public function showRegister() {
        if ($this->isLoggedIn()) {
            // Redirect based on user role
            $role = $this->getUserRole();
            switch ($role) {
                case 'admin':
                    $this->redirect('/admin/dashboard');
                    break;
                case 'organizer':
                    $this->redirect('/organizer/dashboard');
                    break;
                case 'sponsor':
                    $this->redirect('/sponsor/dashboard');
                    break;
                case 'supplier':
                    $this->redirect('/supplier/dashboard');
                    break;
                default:
                    $this->redirect('/participant/home');
            }
        }
        
        $categories = $this->categoryModel->getAll();
        $this->view('auth/register', ['categories' => $categories]);
    }
    
    /**
     * Handle registration
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/register');
        }
        
        $fullName = $this->sanitize($this->post('full_name'));
        $email = $this->sanitize($this->post('email'));
        $password = $this->post('password');
        $confirmPassword = $this->post('confirm_password');
        $role = $this->sanitize($this->post('role'));
        $phone = $this->sanitize($this->post('phone'));
        $location = $this->sanitize($this->post('location'));
        $interests = $this->post('interests', []);
        
        // Validation
        $errors = [];
        
        if (empty($fullName)) {
            $errors[] = 'Full name is required';
        }
        
        if (empty($email) || !$this->validateEmail($email)) {
            $errors[] = 'Valid email is required';
        }
        
        if (empty($password) || strlen($password) < PASSWORD_MIN_LENGTH) {
            $errors[] = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters';
        }
        
        if ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match';
        }
        
        if (empty($role) || !in_array($role, ['participant', 'organizer', 'sponsor', 'supplier'])) {
            $errors[] = 'Please select a valid role';
        }
        
        // Check if email exists
        if ($this->userModel->findByEmail($email)) {
            $errors[] = 'Email already registered';
        }
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            $this->redirect('/register');
        }
        
        // Create user
        $userData = [
            'full_name' => $fullName,
            'email' => $email,
            'password' => $password,
            'phone' => $phone,
            'location' => $location,
            'role' => $role,
            'interests' => $interests
        ];
        
        $userId = $this->userModel->create($userData);
        
        if ($userId) {
            $_SESSION['success'] = 'Registration successful! Please login.';
            $this->redirect('/login');
        } else {
            $_SESSION['error'] = 'Registration failed. Please try again.';
            $this->redirect('/register');
        }
    }
    
    /**
     * Handle logout
     */
    public function logout() {
        // Clear all session variables
        $_SESSION = array();
        
        // Destroy the session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Destroy the session
        session_destroy();
        
        // Redirect to login
        $this->redirect('/login');
    }
}
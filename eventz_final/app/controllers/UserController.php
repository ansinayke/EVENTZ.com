<?php
/**
 * User Controller
 * Handles user profile, settings, and search
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/User.php';

class UserController extends Controller {
    private $userModel;
    
    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
    }
    
    public function profile() {
        $this->requireAuth();
        
        $userId = $this->getUserId();
        $user = $this->userModel->findById($userId);
        $interests = $this->userModel->getInterests($userId);
        
        $this->view('shared/profile', ['user' => $user, 'interests' => $interests]);
    }
    
    public function updateProfile() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false], 400);
        }
        
        $userId = $this->getUserId();
        
        $updateData = [
            'full_name' => $this->sanitize($this->post('full_name')),
            'phone' => $this->sanitize($this->post('phone')),
            'bio' => $this->sanitize($this->post('bio')),
            'website' => $this->sanitize($this->post('website')),
            'location' => $this->sanitize($this->post('location'))
        ];
        
        // Handle avatar upload
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $upload = $this->uploadFile($_FILES['avatar'], AVATAR_PATH, ALLOWED_IMAGE_TYPES, MAX_FILE_SIZE);
            if ($upload['success']) {
                $updateData['avatar_url'] = '/uploads/avatars/' . $upload['filename'];
            }
        }
        
        if ($this->userModel->update($userId, $updateData)) {
            $_SESSION['user_name'] = $updateData['full_name'];
            if (isset($updateData['avatar_url'])) {
                $_SESSION['user_avatar'] = $updateData['avatar_url'];
            }
            $this->json(['success' => true, 'message' => 'Profile updated']);
        } else {
            $this->json(['success' => false], 500);
        }
    }
    
    public function settings() {
        $this->requireAuth();
        $this->view('shared/settings');
    }
    
    public function search() {
        $this->requireAuth();
        
        $query = $this->get('q', '');
        $type = $this->get('type', 'all');
        
        $results = [];
        
        if ($query) {
            if ($type === 'all' || $type === 'users') {
                $results['users'] = $this->userModel->search($query);
            }
            
            if ($type === 'all' || $type === 'events') {
                require_once __DIR__ . '/../models/Event.php';
                $eventModel = new Event();
                $results['events'] = $eventModel->search($query);
            }
        }
        
        $this->view('shared/search', ['results' => $results, 'query' => $query]);
    }
    
    public function follow() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false], 400);
        }
        
        $followingId = $this->post('user_id');
        $followerId = $this->getUserId();
        
        if ($followerId == $followingId) {
            $this->json(['success' => false, 'message' => 'Cannot follow yourself'], 400);
        }
        
        if ($this->userModel->follow($followerId, $followingId)) {
            $this->json(['success' => true, 'message' => 'Following']);
        } else {
            $this->json(['success' => false], 500);
        }
    }
    
    public function unfollow() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false], 400);
        }
        
        $followingId = $this->post('user_id');
        $followerId = $this->getUserId();
        
        if ($this->userModel->unfollow($followerId, $followingId)) {
            $this->json(['success' => true, 'message' => 'Unfollowed']);
        } else {
            $this->json(['success' => false], 500);
        }
    }
}
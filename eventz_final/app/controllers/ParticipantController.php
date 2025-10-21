<?php
/**
 * Participant Controller
 * Handles participant-specific functionality
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../models/Category.php';

class ParticipantController extends Controller {
    private $userModel;
    private $eventModel;
    private $categoryModel;
    
    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
        $this->eventModel = new Event();
        $this->categoryModel = new Category();
    }
    
    /**
     * Participant home page (personalized feed)
     */
    public function home() {
        $this->requireRole('participant');
        
        $userId = $this->getUserId();
        
        // Get recommended events based on interests and following
        $recommendedEvents = $this->eventModel->getRecommended($userId, 20);
        
        // Get user's interests
        $interests = $this->userModel->getInterests($userId);
        
        // Get following
        $following = $this->userModel->getFollowing($userId);
        
        // Get ongoing events with videos (Lives)
        $ongoingEvents = $this->eventModel->getAll([
            'event_status' => 'ongoing',
            'status' => 'approved',
            'limit' => 10
        ]);
        
        $data = [
            'recommendedEvents' => $recommendedEvents,
            'interests' => $interests,
            'following' => $following,
            'ongoingEvents' => $ongoingEvents
        ];
        
        $this->view('participant/home', $data);
    }
    
    /**
     * Explore events page
     */
    public function explore() {
        $this->requireRole('participant');
        
        $search = $this->get('search', '');
        $categoryId = $this->get('category');
        $dateFrom = $this->get('date_from');
        $dateTo = $this->get('date_to');
        
        $filters = ['status' => 'approved'];
        
        if ($categoryId) {
            $filters['category_id'] = $categoryId;
        }
        
        if ($search) {
            $events = $this->eventModel->search($search, $filters);
        } else {
            $events = $this->eventModel->getAll($filters);
        }
        
        // Get all categories for filter
        $categories = $this->categoryModel->getAll();
        
        // Get top participants (most events attended)
        $topParticipants = $this->getTopParticipants();
        
        $data = [
            'events' => $events,
            'categories' => $categories,
            'topParticipants' => $topParticipants,
            'search' => $search,
            'selectedCategory' => $categoryId
        ];
        
        $this->view('participant/explore', $data);
    }
    
    /**
     * Participant portfolio page
     */
    public function portfolio() {
        $this->requireRole('participant');
        
        $userId = $this->getUserId();
        
        // Get user info
        $user = $this->userModel->findById($userId);
        
        // Get attended events
        $attendedEvents = $this->eventModel->getUserEvents($userId, 'attended');
        
        // Get registered events
        $registeredEvents = $this->eventModel->getUserEvents($userId, 'registered');
        
        // Get stats
        $stats = [
            'total_attended' => count($attendedEvents),
            'total_registered' => count($registeredEvents),
            'followers' => $this->userModel->getFollowerCount($userId),
            'following' => $this->userModel->getFollowingCount($userId)
        ];
        
        $data = [
            'user' => $user,
            'attendedEvents' => $attendedEvents,
            'registeredEvents' => $registeredEvents,
            'stats' => $stats
        ];
        
        $this->view('participant/portfolio', $data);
    }
    
    /**
     * Register for event
     */
    public function registerEvent() {
        $this->requireRole('participant');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }
        
        $eventId = $this->post('event_id');
        $userId = $this->getUserId();
        
        if (!$eventId) {
            $this->json(['success' => false, 'message' => 'Event ID required'], 400);
        }
        
        // Check if already registered
        if ($this->eventModel->isRegistered($eventId, $userId)) {
            $this->json(['success' => false, 'message' => 'Already registered for this event'], 400);
        }
        
        // Register
        if ($this->eventModel->register($eventId, $userId)) {
            $this->json(['success' => true, 'message' => 'Successfully registered for event']);
        } else {
            $this->json(['success' => false, 'message' => 'Registration failed'], 500);
        }
    }
    
    /**
     * Mark event participation
     */
    public function markParticipation() {
        $this->requireRole('participant');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }
        
        $eventId = $this->post('event_id');
        $userId = $this->getUserId();
        
        if (!$eventId) {
            $this->json(['success' => false, 'message' => 'Event ID required'], 400);
        }
        
        // Mark participation
        if ($this->eventModel->markParticipation($eventId, $userId)) {
            $this->json(['success' => true, 'message' => 'Participation marked successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to mark participation'], 500);
        }
    }
    
    /**
     * Unregister from event
     */
    public function unregisterEvent() {
        $this->requireRole('participant');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }
        
        $eventId = $this->post('event_id');
        $userId = $this->getUserId();
        
        if (!$eventId) {
            $this->json(['success' => false, 'message' => 'Event ID required'], 400);
        }
        
        // Check if registered
        if (!$this->eventModel->isRegistered($eventId, $userId)) {
            $this->json(['success' => false, 'message' => 'Not registered for this event'], 400);
        }
        
        // Unregister
        if ($this->eventModel->unregister($eventId, $userId)) {
            $this->json(['success' => true, 'message' => 'Successfully unregistered from event']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to unregister'], 500);
        }
    }
    
    /**
     * Get event details (AJAX)
     */
    public function getEventDetails() {
        $this->requireAuth();
        
        $eventId = $this->get('id');
        
        if (!$eventId) {
            $this->json(['success' => false, 'message' => 'Event ID required'], 400);
        }
        
        $event = $this->eventModel->findById($eventId);
        
        if (!$event) {
            $this->json(['success' => false, 'message' => 'Event not found'], 404);
        }
        
        // Track view
        $this->eventModel->trackView($eventId, $this->getUserId());
        
        // Check if user is registered
        $event['is_registered'] = $this->eventModel->isRegistered($eventId, $this->getUserId());
        
        $this->json(['success' => true, 'event' => $event]);
    }
    
    /**
     * Get top participants
     */
    private function getTopParticipants($limit = 10) {
        $sql = "SELECT u.id, u.full_name, u.avatar_url, 
                COUNT(er.id) as participation_count
                FROM users u
                INNER JOIN user_roles ur ON u.id = ur.user_id
                INNER JOIN roles r ON ur.role_id = r.id
                LEFT JOIN event_registrations er ON u.id = er.user_id AND er.status = 'attended'
                WHERE r.name = 'participant'
                GROUP BY u.id
                ORDER BY participation_count DESC
                LIMIT :limit";
        
        return $this->db->fetchAll($sql, [':limit' => $limit]);
    }
}
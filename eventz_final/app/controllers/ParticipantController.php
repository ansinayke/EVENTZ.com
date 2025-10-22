<?php
/**
 * Participant Controller
 * Handles participant-specific functionality
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Event.php';

class ParticipantController extends Controller {
    private $userModel;
    private $eventModel;
    
    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
        $this->eventModel = new Event();
    }
    
    /**
     * Participant home page
     */
    public function home() {
        $this->requireAuth();
        
        $userId = $this->getUserId();
        $user = $this->userModel->findById($userId);
        
        // Get user's registered events
        $registeredEvents = $this->eventModel->getUserRegistrations($userId);
        
        // Get recommended events based on interests
        $interests = $this->userModel->getInterests($userId);
        $recommendedEvents = [];
        if (!empty($interests)) {
            $categoryIds = array_column($interests, 'id');
            $recommendedEvents = $this->eventModel->getEventsByCategories($categoryIds, 6);
        }
        
        $this->view('participant/home', [
            'user' => $user,
            'registeredEvents' => $registeredEvents,
            'recommendedEvents' => $recommendedEvents
        ]);
    }
    
    /**
     * Explore events page
     */
    public function explore() {
        $this->requireAuth();
        
        $category = $this->get('category', '');
        $search = $this->get('search', '');
        
        $events = $this->eventModel->getApprovedEvents($category, $search);
        $categories = $this->eventModel->getCategories();
        
        $this->view('participant/explore', [
            'events' => $events,
            'categories' => $categories,
            'currentCategory' => $category,
            'searchQuery' => $search
        ]);
    }
    
    /**
     * Participant portfolio page
     */
    public function portfolio() {
        $this->requireAuth();

        $userId = $this->getUserId();
        $user = $this->userModel->findById($userId);
        $interests = $this->userModel->getInterests($userId);

        // Get user's statistics
        $stats = $this->userModel->getUserStats($userId);

        // Get user's participated events (attended)
        $participatedEvents = $this->eventModel->getUserParticipationHistory($userId);

        // Get user's registered events (upcoming)
        $registeredEvents = $this->eventModel->getUserRegistrations($userId);

        $this->view('participant/portfolio', [
            'user' => $user,
            'interests' => $interests,
            'stats' => $stats,
            'participatedEvents' => $participatedEvents,
            'registeredEvents' => $registeredEvents
        ]);
    }
    
    /**
     * Register for an event
     */
    public function registerEvent() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method'], 400);
        }
        
        $eventId = $this->post('event_id');
        $userId = $this->getUserId();
        
        if (empty($eventId)) {
            $this->json(['success' => false, 'message' => 'Event ID is required'], 400);
        }
        
        // Check if already registered
        if ($this->eventModel->isUserRegistered($eventId, $userId)) {
            $this->json(['success' => false, 'message' => 'Already registered for this event'], 400);
        }
        
        // Register user for event
        if ($this->eventModel->registerUser($eventId, $userId)) {
            $this->json(['success' => true, 'message' => 'Successfully registered for the event']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to register for the event'], 500);
        }
    }
    
    /**
     * Unregister from an event
     */
    public function unregisterEvent() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method'], 400);
        }
        
        $eventId = $this->post('event_id');
        $userId = $this->getUserId();
        
        if (empty($eventId)) {
            $this->json(['success' => false, 'message' => 'Event ID is required'], 400);
        }
        
        if ($this->eventModel->unregisterUser($eventId, $userId)) {
            $this->json(['success' => true, 'message' => 'Successfully unregistered from the event']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to unregister from the event'], 500);
        }
    }
    
    /**
     * Mark participation in an event
     */
    public function markParticipation() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method'], 400);
        }
        
        $eventId = $this->post('event_id');
        $userId = $this->getUserId();
        $status = $this->post('status', 'attended'); // attended, no_show
        
        if (empty($eventId)) {
            $this->json(['success' => false, 'message' => 'Event ID is required'], 400);
        }
        
        if ($this->eventModel->updateParticipationStatus($eventId, $userId, $status)) {
            $this->json(['success' => true, 'message' => 'Participation status updated']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to update participation status'], 500);
        }
    }
    
    /**
     * Get event details
     */
    public function getEventDetails() {
        $this->requireAuth();
        
        $eventId = $this->get('event_id');
        
        if (empty($eventId)) {
            $this->json(['success' => false, 'message' => 'Event ID is required'], 400);
        }
        
        $event = $this->eventModel->findById($eventId);
        
        if (!$event) {
            $this->json(['success' => false, 'message' => 'Event not found'], 404);
        }
        
        $userId = $this->getUserId();
        $event['is_registered'] = $this->eventModel->isUserRegistered($eventId, $userId);
        
        $this->json(['success' => true, 'event' => $event]);
    }
}

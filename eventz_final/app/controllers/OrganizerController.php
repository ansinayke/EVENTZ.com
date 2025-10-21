<?php
/**
 * Organizer Controller - FIXED VERSION
 * Handles organizer-specific functionality
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../models/Category.php';

class OrganizerController extends Controller {
    private $userModel;
    private $eventModel;
    private $categoryModel;
    
    public function __construct() {
        parent::__construct();
        try {
            $this->userModel = new User();
            $this->eventModel = new Event();
            $this->categoryModel = new Category();
        } catch (Exception $e) {
            error_log("OrganizerController constructor error: " . $e->getMessage());
            die("Database connection error. Please check your configuration.");
        }
    }
    
    /**
     * Organizer home page
     */
    public function home() {
        $this->requireRole('organizer');
        
        $userId = $this->getUserId();
        
        try {
            // Get events from following
            $feedEvents = $this->eventModel->getFromFollowing($userId, 20);
            
            // Get following list
            $following = $this->userModel->getFollowing($userId);
            
            // Get ongoing events with videos (Lives)
            $liveEvents = $this->eventModel->getOngoingWithVideos($userId);
            
            $data = [
                'feedEvents' => $feedEvents ?: [],
                'following' => $following ?: [],
                'liveEvents' => $liveEvents ?: []
            ];
            
            $this->view('organizer/home', $data);
        } catch (Exception $e) {
            error_log("Organizer home error: " . $e->getMessage());
            $this->view('organizer/home', ['feedEvents' => [], 'following' => [], 'liveEvents' => []]);
        }
    }
    
    /**
     * Organizer dashboard
     */
    public function dashboard() {
        $this->requireRole('organizer');
        
        $userId = $this->getUserId();
        
        try {
            // Get organizer's events by status
            $upcomingEvents = $this->eventModel->getByOrganizer($userId, 'upcoming');
            $ongoingEvents = $this->eventModel->getByOrganizer($userId, 'ongoing');
            $completedEvents = $this->eventModel->getByOrganizer($userId, 'completed');
            
            // Get categories for event creation
            $categories = $this->categoryModel->getAll();
            
            $data = [
                'upcomingEvents' => $upcomingEvents ?: [],
                'ongoingEvents' => $ongoingEvents ?: [],
                'completedEvents' => $completedEvents ?: [],
                'categories' => $categories ?: []
            ];
            
            $this->view('organizer/dashboard', $data);
        } catch (Exception $e) {
            error_log("Organizer dashboard error: " . $e->getMessage());
            $this->view('organizer/dashboard', [
                'upcomingEvents' => [],
                'ongoingEvents' => [],
                'completedEvents' => [],
                'categories' => []
            ]);
        }
    }
    
    /**
     * Organizer analytics
     */
    public function analytics() {
        $this->requireRole('organizer');
        
        $userId = $this->getUserId();
        
        try {
            // Get all events
            $allEvents = $this->eventModel->getByOrganizer($userId);
            
            // Calculate statistics
            $stats = [
                'total_events' => count($allEvents),
                'upcoming_events' => count(array_filter($allEvents, fn($e) => $e['event_status'] === 'upcoming')),
                'ongoing_events' => count(array_filter($allEvents, fn($e) => $e['event_status'] === 'ongoing')),
                'completed_events' => count(array_filter($allEvents, fn($e) => $e['event_status'] === 'completed')),
                'total_participants' => 0,
                'total_views' => 0,
                'followers' => $this->userModel->getFollowerCount($userId)
            ];
            
            // Calculate total participants and views
            foreach ($allEvents as $event) {
                $stats['total_participants'] += $event['registration_count'] ?? 0;
                $stats['total_views'] += $event['view_count'] ?? 0;
            }
            
            // Get events by category
            $eventsByCategory = [];
            foreach ($allEvents as $event) {
                $categories = explode(',', $event['category_names'] ?? 'Uncategorized');
                foreach ($categories as $category) {
                    $category = trim($category);
                    if (!isset($eventsByCategory[$category])) {
                        $eventsByCategory[$category] = 0;
                    }
                    $eventsByCategory[$category]++;
                }
            }
            
            $data = [
                'stats' => $stats,
                'eventsByCategory' => $eventsByCategory,
                'recentEvents' => array_slice($allEvents, 0, 10)
            ];
            
            $this->view('organizer/analytics', $data);
        } catch (Exception $e) {
            error_log("Organizer analytics error: " . $e->getMessage());
            $this->view('organizer/analytics', [
                'stats' => [
                    'total_events' => 0, 
                    'upcoming_events' => 0, 
                    'ongoing_events' => 0, 
                    'completed_events' => 0, 
                    'total_participants' => 0, 
                    'total_views' => 0, 
                    'followers' => 0
                ],
                'eventsByCategory' => [],
                'recentEvents' => []
            ]);
        }
    }
    
    /**
     * Create event - FIXED VERSION
     */
    public function createEvent() {
        $this->requireRole('organizer');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }
        
        $userId = $this->getUserId();
        
        try {
            // Get and validate form data
            $title = $this->sanitize($this->post('title'));
            $description = $this->sanitize($this->post('description'));
            $categoryId = $this->post('category_id');
            $eventDate = $this->post('event_date');
            $eventTime = $this->post('event_time');
            $location = $this->sanitize($this->post('location'));
            $venue = $this->sanitize($this->post('venue'));
            
            // Validate required fields
            if (empty($title) || empty($description) || empty($categoryId) || 
                empty($eventDate) || empty($eventTime) || empty($location) || empty($venue)) {
                $this->json(['success' => false, 'message' => 'Please fill in all required fields'], 400);
            }
            
            // Prepare event data matching database schema
            $eventData = [
                'organizer_id' => $userId,
                'title' => $title,
                'description' => $description,
                'category_id' => $categoryId,
                'location_text' => $location,
                'venue_name' => $venue,
                'address' => $this->sanitize($this->post('address')),
                'city' => $this->sanitize($this->post('city')),
                'country' => $this->sanitize($this->post('country')),
                'start_at' => $eventDate . ' ' . $eventTime,
                'end_at' => $this->post('end_date') && $this->post('end_time') 
                    ? $this->post('end_date') . ' ' . $this->post('end_time')
                    : $eventDate . ' ' . $eventTime,
                'capacity' => $this->post('capacity') ? (int)$this->post('capacity') : null,
                'registration_deadline' => $this->post('registration_deadline'),
                'is_public' => $this->post('is_public') ?? true
            ];
            
            // Handle banner upload
            if (isset($_FILES['banner']) && $_FILES['banner']['error'] === UPLOAD_ERR_OK) {
                try {
                    // Ensure upload directory exists
                    if (!is_dir(EVENT_IMAGE_PATH)) {
                        mkdir(EVENT_IMAGE_PATH, 0755, true);
                    }
                    
                    $upload = $this->uploadFile($_FILES['banner'], EVENT_IMAGE_PATH, ALLOWED_IMAGE_TYPES, MAX_FILE_SIZE);
                    if ($upload['success']) {
                        $eventData['banner_url'] = '/uploads/events/' . $upload['filename'];
                    } else {
                        error_log("Upload failed: " . $upload['message']);
                    }
                } catch (Exception $e) {
                    error_log("Upload error: " . $e->getMessage());
                }
            }
            
            // Create event
            $eventId = $this->eventModel->create($eventData);
            
            if ($eventId) {
                $this->json([
                    'success' => true, 
                    'message' => 'Event created successfully and pending admin approval', 
                    'event_id' => $eventId
                ]);
            } else {
                $this->json(['success' => false, 'message' => 'Failed to create event'], 500);
            }
        } catch (Exception $e) {
            error_log("Create event error: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Update event
     */
    public function updateEvent() {
        $this->requireRole('organizer');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }
        
        $eventId = $this->post('event_id');
        $userId = $this->getUserId();
        
        try {
            // Verify ownership
            $event = $this->eventModel->findById($eventId);
            if (!$event || $event['organizer_id'] != $userId) {
                $this->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            
            // Prepare update data matching database schema
            $updateData = [
                'title' => $this->sanitize($this->post('title')),
                'description' => $this->sanitize($this->post('description')),
                'category_id' => $this->post('category_id'),
                'location_text' => $this->sanitize($this->post('location')),
                'venue_name' => $this->sanitize($this->post('venue')),
                'address' => $this->sanitize($this->post('address')),
                'city' => $this->sanitize($this->post('city')),
                'country' => $this->sanitize($this->post('country')),
                'start_at' => $this->post('event_date') . ' ' . $this->post('event_time'),
                'capacity' => $this->post('capacity') ? (int)$this->post('capacity') : null,
                'registration_deadline' => $this->post('registration_deadline')
            ];
            
            // Add end date/time if provided
            if ($this->post('end_date') && $this->post('end_time')) {
                $updateData['end_at'] = $this->post('end_date') . ' ' . $this->post('end_time');
            }
            
            // Handle banner upload
            if (isset($_FILES['banner']) && $_FILES['banner']['error'] === UPLOAD_ERR_OK) {
                try {
                    if (!is_dir(EVENT_IMAGE_PATH)) {
                        mkdir(EVENT_IMAGE_PATH, 0755, true);
                    }
                    
                    $upload = $this->uploadFile($_FILES['banner'], EVENT_IMAGE_PATH, ALLOWED_IMAGE_TYPES, MAX_FILE_SIZE);
                    if ($upload['success']) {
                        $updateData['banner_url'] = '/uploads/events/' . $upload['filename'];
                    }
                } catch (Exception $e) {
                    error_log("Upload error: " . $e->getMessage());
                }
            }
            
            // Update event
            if ($this->eventModel->update($eventId, $updateData)) {
                $this->json(['success' => true, 'message' => 'Event updated successfully']);
            } else {
                $this->json(['success' => false, 'message' => 'Failed to update event'], 500);
            }
        } catch (Exception $e) {
            error_log("Update event error: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'An error occurred while updating the event'], 500);
        }
    }
    
    /**
     * Delete event
     */
    public function deleteEvent() {
        $this->requireRole('organizer');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }
        
        $eventId = $this->post('event_id');
        $userId = $this->getUserId();
        
        try {
            // Verify ownership
            $event = $this->eventModel->findById($eventId);
            if (!$event || $event['organizer_id'] != $userId) {
                $this->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            
            // Delete event
            if ($this->eventModel->delete($eventId)) {
                $this->json(['success' => true, 'message' => 'Event deleted successfully']);
            } else {
                $this->json(['success' => false, 'message' => 'Failed to delete event'], 500);
            }
        } catch (Exception $e) {
            error_log("Delete event error: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'An error occurred while deleting the event'], 500);
        }
    }
    
    /**
     * Upload event video
     */
    public function uploadVideo() {
        $this->requireRole('organizer');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }
        
        $eventId = $this->post('event_id');
        $userId = $this->getUserId();
        
        try {
            // Verify ownership
            $event = $this->eventModel->findById($eventId);
            if (!$event || $event['organizer_id'] != $userId) {
                $this->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            
            // Check if event is ongoing
            if ($event['event_status'] !== 'ongoing') {
                $this->json(['success' => false, 'message' => 'Can only upload videos for ongoing events'], 400);
            }
            
            // Handle video upload
            if (!isset($_FILES['video']) || $_FILES['video']['error'] !== UPLOAD_ERR_OK) {
                $this->json(['success' => false, 'message' => 'No video uploaded'], 400);
            }
            
            if (!is_dir(VIDEO_PATH)) {
                mkdir(VIDEO_PATH, 0755, true);
            }
            
            $upload = $this->uploadFile($_FILES['video'], VIDEO_PATH, ALLOWED_VIDEO_TYPES, MAX_VIDEO_SIZE);
            
            if ($upload['success']) {
                $videoUrl = '/uploads/videos/' . $upload['filename'];
                
                if ($this->eventModel->addVideo($eventId, $videoUrl)) {
                    $this->json(['success' => true, 'message' => 'Video uploaded successfully', 'video_url' => $videoUrl]);
                } else {
                    $this->json(['success' => false, 'message' => 'Failed to save video'], 500);
                }
            } else {
                $this->json(['success' => false, 'message' => $upload['message']], 400);
            }
        } catch (Exception $e) {
            error_log("Upload video error: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'An error occurred while uploading the video'], 500);
        }
    }
    
    /**
     * Public profile
     */
    public function profile() {
        $organizerId = $this->get('id');
        
        if (!$organizerId) {
            $this->redirect('/');
        }
        
        try {
            $organizer = $this->userModel->findById($organizerId);
            
            if (!$organizer || !$this->userModel->hasRole($organizerId, 'organizer')) {
                $this->redirect('/');
            }
            
            // Get organizer's events
            $events = $this->eventModel->getByOrganizer($organizerId);
            
            // Get stats
            $stats = [
                'total_events' => count($events),
                'completed_events' => count(array_filter($events, fn($e) => $e['event_status'] === 'completed')),
                'followers' => $this->userModel->getFollowerCount($organizerId),
                'following' => $this->userModel->getFollowingCount($organizerId)
            ];
            
            // Check if current user is following
            $isFollowing = false;
            if ($this->isLoggedIn()) {
                $isFollowing = $this->userModel->isFollowing($this->getUserId(), $organizerId);
            }
            
            $data = [
                'organizer' => $organizer,
                'events' => $events ?: [],
                'stats' => $stats,
                'isFollowing' => $isFollowing
            ];
            
            $this->view('organizer/profile', $data);
        } catch (Exception $e) {
            error_log("Organizer profile error: " . $e->getMessage());
            $this->redirect('/');
        }
    }
}
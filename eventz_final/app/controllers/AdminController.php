<?php
/**
 * Admin Controller - FIXED VERSION
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Event.php';

class AdminController extends Controller {
    private $userModel;
    private $eventModel;
    
    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
        $this->eventModel = new Event();
    }
    
    public function dashboard() {
        $this->requireRole('admin');
        
        // Get pending events (status = 'pending')
        $pendingEvents = $this->eventModel->getAll(['status' => 'pending']);
        
        // Get approved events (status = 'approved')
        $approvedEvents = $this->eventModel->getAll(['status' => 'approved']);
        
        $this->view('admin/dashboard', [
            'pendingEvents' => $pendingEvents ?: [],
            'approvedEvents' => $approvedEvents ?: []
        ]);
    }
    
    public function analytics() {
        $this->requireRole('admin');
        
        // Get system-wide statistics
        $stats = $this->getSystemStats();
        
        $this->view('admin/analytics', ['stats' => $stats]);
    }
    
    public function approveEvent() {
        $this->requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }
        
        $eventId = $this->post('event_id');
        $adminId = $this->getUserId();
        
        if (!$eventId) {
            $this->json(['success' => false, 'message' => 'Event ID required'], 400);
        }
        
        if ($this->eventModel->approve($eventId, $adminId)) {
            $this->json(['success' => true, 'message' => 'Event approved successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to approve event'], 500);
        }
    }
    
    public function rejectEvent() {
        $this->requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }
        
        $eventId = $this->post('event_id');
        
        if (!$eventId) {
            $this->json(['success' => false, 'message' => 'Event ID required'], 400);
        }
        
        if ($this->eventModel->reject($eventId)) {
            $this->json(['success' => true, 'message' => 'Event rejected successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to reject event'], 500);
        }
    }
    
    public function deleteEvent() {
        $this->requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }
        
        $eventId = $this->post('event_id');
        
        if (!$eventId) {
            $this->json(['success' => false, 'message' => 'Event ID required'], 400);
        }
        
        if ($this->eventModel->delete($eventId)) {
            $this->json(['success' => true, 'message' => 'Event deleted successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to delete event'], 500);
        }
    }
    
    private function getSystemStats() {
        $sql = "SELECT 
                (SELECT COUNT(*) FROM users) as total_users,
                (SELECT COUNT(*) FROM users u INNER JOIN user_roles ur ON u.id = ur.user_id INNER JOIN roles r ON ur.role_id = r.id WHERE r.name = 'organizer') as total_organizers,
                (SELECT COUNT(*) FROM users u INNER JOIN user_roles ur ON u.id = ur.user_id INNER JOIN roles r ON ur.role_id = r.id WHERE r.name = 'participant') as total_participants,
                (SELECT COUNT(*) FROM users u INNER JOIN user_roles ur ON u.id = ur.user_id INNER JOIN roles r ON ur.role_id = r.id WHERE r.name = 'sponsor') as total_sponsors,
                (SELECT COUNT(*) FROM users u INNER JOIN user_roles ur ON u.id = ur.user_id INNER JOIN roles r ON ur.role_id = r.id WHERE r.name = 'supplier') as total_suppliers,
                (SELECT COUNT(*) FROM events) as total_events,
                (SELECT COUNT(*) FROM events WHERE status = 'approved') as approved_events,
                (SELECT COUNT(*) FROM events WHERE status = 'pending') as pending_events,
                (SELECT COUNT(*) FROM events WHERE status = 'rejected') as rejected_events,
                (SELECT COUNT(*) FROM event_registrations) as total_registrations";
        
        return $this->db->fetchOne($sql);
    }
}
<?php
/**
 * Sponsor Controller - FIXED VERSION
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../models/Sponsorship.php';

class SponsorController extends Controller {
    private $userModel;
    private $eventModel;
    private $sponsorshipModel;
    
    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
        $this->eventModel = new Event();
        $this->sponsorshipModel = new Sponsorship();
    }
    
    public function home() {
        $this->requireRole('sponsor');
        $userId = $this->getUserId();
        
        // Get events from following organizers
        $feedEvents = $this->eventModel->getFromFollowing($userId, 20);
        
        // Get following list
        $following = $this->userModel->getFollowing($userId);
        
        // Get ongoing events with videos (Lives)
        $liveEvents = $this->eventModel->getOngoingWithVideos($userId);
        
        $this->view('sponsor/home', [
            'feedEvents' => $feedEvents ?: [],
            'following' => $following ?: [],
            'liveEvents' => $liveEvents ?: []
        ]);
    }
    
    public function dashboard() {
        $this->requireRole('sponsor');
        $userId = $this->getUserId();
        
        // Get sponsor's plans
        $plans = $this->sponsorshipModel->getPlansBySponsor($userId);
        
        // Get sponsor's sponsorships
        $sponsorships = $this->sponsorshipModel->getSponsorSponsorships($userId);
        
        // Get approved upcoming events
        $events = $this->eventModel->getAll([
            'status' => 'approved',
            'event_status' => 'upcoming'
        ]);
        
        $this->view('sponsor/dashboard', [
            'plans' => $plans ?: [],
            'sponsorships' => $sponsorships ?: [],
            'events' => $events ?: []
        ]);
    }
    
    public function analytics() {
        $this->requireRole('sponsor');
        $userId = $this->getUserId();
        
        // Get statistics
        $stats = $this->sponsorshipModel->getSponsorStats($userId);
        
        // Get all sponsorships
        $sponsorships = $this->sponsorshipModel->getSponsorSponsorships($userId);
        
        $this->view('sponsor/analytics', [
            'stats' => $stats,
            'sponsorships' => $sponsorships ?: []
        ]);
    }
    
    public function createPlan() {
        $this->requireRole('sponsor');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }
        
        $planData = [
            'sponsor_id' => $this->getUserId(),
            'plan_name' => $this->sanitize($this->post('plan_name')),
            'description' => $this->sanitize($this->post('description')),
            'amount' => $this->post('amount'), // Will be converted to 'price' in model
            'benefits' => $this->sanitize($this->post('benefits')),
            'duration_days' => $this->post('duration_days', 30)
        ];
        
        // Validate required fields
        if (empty($planData['plan_name']) || empty($planData['amount'])) {
            $this->json(['success' => false, 'message' => 'Plan name and amount are required'], 400);
        }
        
        $planId = $this->sponsorshipModel->createPlan($planData);
        
        if ($planId) {
            $this->json(['success' => true, 'message' => 'Plan created successfully', 'plan_id' => $planId]);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to create plan'], 500);
        }
    }
    
    public function updatePlan() {
        $this->requireRole('sponsor');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }
        
        $planId = $this->post('plan_id');
        $userId = $this->getUserId();
        
        // Verify ownership
        $plan = $this->sponsorshipModel->getPlanById($planId);
        if (!$plan || $plan['sponsor_id'] != $userId) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $updateData = [
            'plan_name' => $this->sanitize($this->post('plan_name')),
            'description' => $this->sanitize($this->post('description')),
            'amount' => $this->post('amount'),
            'benefits' => $this->sanitize($this->post('benefits')),
            'duration_days' => $this->post('duration_days')
        ];
        
        if ($this->sponsorshipModel->updatePlan($planId, $updateData)) {
            $this->json(['success' => true, 'message' => 'Plan updated successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to update plan'], 500);
        }
    }
    
    public function deletePlan() {
        $this->requireRole('sponsor');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }
        
        $planId = $this->post('plan_id');
        $userId = $this->getUserId();
        
        // Verify ownership
        $plan = $this->sponsorshipModel->getPlanById($planId);
        if (!$plan || $plan['sponsor_id'] != $userId) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        if ($this->sponsorshipModel->deletePlan($planId)) {
            $this->json(['success' => true, 'message' => 'Plan deleted successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to delete plan'], 500);
        }
    }
    
    public function sponsorEvent() {
        $this->requireRole('sponsor');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }
        
        $agreementData = [
            'event_id' => $this->post('event_id'),
            'sponsor_id' => $this->getUserId(),
            'plan_id' => $this->post('plan_id'),
            'amount' => $this->post('amount'),
            'status' => 'pending'
        ];
        
        // Validate required fields
        if (empty($agreementData['event_id']) || empty($agreementData['amount'])) {
            $this->json(['success' => false, 'message' => 'Event ID and amount are required'], 400);
        }
        
        $agreementId = $this->sponsorshipModel->createAgreement($agreementData);
        
        if ($agreementId) {
            $this->json(['success' => true, 'message' => 'Sponsorship request submitted', 'agreement_id' => $agreementId]);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to create sponsorship'], 500);
        }
    }
    
    public function profile() {
        $sponsorId = $this->get('id');
        
        if (!$sponsorId) {
            $this->redirect('/');
        }
        
        $sponsor = $this->userModel->findById($sponsorId);
        
        if (!$sponsor || !$this->userModel->hasRole($sponsorId, 'sponsor')) {
            $this->redirect('/');
        }
        
        // Get sponsor's plans
        $plans = $this->sponsorshipModel->getPlansBySponsor($sponsorId);
        
        // Get sponsor's sponsorships
        $sponsorships = $this->sponsorshipModel->getSponsorSponsorships($sponsorId);
        
        // Get stats
        $stats = $this->sponsorshipModel->getSponsorStats($sponsorId);
        
        // Check if current user is following
        $isFollowing = false;
        if ($this->isLoggedIn()) {
            $isFollowing = $this->userModel->isFollowing($this->getUserId(), $sponsorId);
        }
        
        $this->view('sponsor/profile', [
            'sponsor' => $sponsor,
            'plans' => $plans ?: [],
            'sponsorships' => $sponsorships ?: [],
            'stats' => $stats,
            'isFollowing' => $isFollowing
        ]);
    }
}
<?php
/**
 * Event Model
 * Handles all event-related database operations
 * FIXED VERSION - Matches database schema exactly
 */

class Event {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Create a new event
     */
    public function create($data) {
        $sql = "INSERT INTO events (
                    organizer_id, title, description, location_text, venue_name, 
                    address, city, country, start_at, end_at, capacity, 
                    status, event_status, banner_url, registration_deadline, 
                    is_public, created_at, updated_at
                ) VALUES (
                    :organizer_id, :title, :description, :location_text, :venue_name, 
                    :address, :city, :country, :start_at, :end_at, :capacity, 
                    :status, :event_status, :banner_url, :registration_deadline, 
                    :is_public, NOW(), NOW()
                )";
        
        $params = [
            ':organizer_id' => $data['organizer_id'],
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':location_text' => $data['location_text'],
            ':venue_name' => $data['venue_name'],
            ':address' => $data['address'] ?? null,
            ':city' => $data['city'] ?? null,
            ':country' => $data['country'] ?? null,
            ':start_at' => $data['start_at'],
            ':end_at' => $data['end_at'],
            ':capacity' => $data['capacity'] ?? null,
            ':status' => 'pending', // Pending admin approval
            ':event_status' => 'upcoming',
            ':banner_url' => $data['banner_url'] ?? null,
            ':registration_deadline' => $data['registration_deadline'] ?? null,
            ':is_public' => $data['is_public'] ?? true
        ];
        
        if ($this->db->execute($sql, $params)) {
            $eventId = $this->db->lastInsertId();
            
            // Add category association if provided
            if (isset($data['category_id']) && $data['category_id']) {
                $this->addCategory($eventId, $data['category_id']);
            }
            
            return $eventId;
        }
        
        return false;
    }
    
    /**
     * Add category to event
     */
    public function addCategory($eventId, $categoryId) {
        $sql = "INSERT INTO event_categories (event_id, category_id) VALUES (:event_id, :category_id)";
        return $this->db->execute($sql, [
            ':event_id' => $eventId,
            ':category_id' => $categoryId
        ]);
    }
    
    /**
     * Update event
     */
    public function update($id, $data) {
        $fields = [];
        $params = [':id' => $id];
        
        $allowedFields = [
            'title', 'description', 'location_text', 'venue_name', 
            'address', 'city', 'country', 'start_at', 'end_at', 
            'capacity', 'registration_deadline', 'banner_url', 'is_public'
        ];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = :$field";
                $params[":$field"] = $data[$field];
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $sql = "UPDATE events SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = :id";
        
        $result = $this->db->execute($sql, $params);
        
        // Update category if provided
        if (isset($data['category_id'])) {
            $this->db->execute("DELETE FROM event_categories WHERE event_id = :id", [':id' => $id]);
            $this->addCategory($id, $data['category_id']);
        }
        
        return $result;
    }
    
    /**
     * Delete event
     */
    public function delete($id) {
        $sql = "DELETE FROM events WHERE id = :id";
        return $this->db->execute($sql, [':id' => $id]);
    }
    
    /**
     * Get event by ID with full details
     */
    public function findById($id) {
        $sql = "SELECT e.*, 
                GROUP_CONCAT(c.name) as category_names,
                GROUP_CONCAT(c.id) as category_ids,
                GROUP_CONCAT(c.color) as category_colors,
                u.full_name as organizer_name, 
                u.avatar_url as organizer_avatar,
                u.bio as organizer_bio,
                (SELECT COUNT(*) FROM event_registrations WHERE event_id = e.id AND status = 'registered') as registration_count,
                (SELECT COUNT(*) FROM event_views WHERE event_id = e.id) as view_count
                FROM events e
                LEFT JOIN event_categories ec ON e.id = ec.event_id
                LEFT JOIN categories c ON ec.category_id = c.id
                LEFT JOIN users u ON e.organizer_id = u.id
                WHERE e.id = :id
                GROUP BY e.id";
        
        return $this->db->fetchOne($sql, [':id' => $id]);
    }
    
    /**
     * Get all events with filters
     */
    public function getAll($filters = []) {
        $sql = "SELECT e.*, 
                GROUP_CONCAT(DISTINCT c.name) as category_names,
                GROUP_CONCAT(DISTINCT c.color) as category_colors,
                u.full_name as organizer_name, 
                u.avatar_url as organizer_avatar,
                (SELECT COUNT(*) FROM event_registrations WHERE event_id = e.id AND status = 'registered') as registration_count
                FROM events e
                LEFT JOIN event_categories ec ON e.id = ec.event_id
                LEFT JOIN categories c ON ec.category_id = c.id
                LEFT JOIN users u ON e.organizer_id = u.id
                WHERE 1=1";
        
        $params = [];
        
        if (isset($filters['status'])) {
            $sql .= " AND e.status = :status";
            $params[':status'] = $filters['status'];
        }
        
        if (isset($filters['event_status'])) {
            $sql .= " AND e.event_status = :event_status";
            $params[':event_status'] = $filters['event_status'];
        }
        
        if (isset($filters['category_id'])) {
            $sql .= " AND ec.category_id = :category_id";
            $params[':category_id'] = $filters['category_id'];
        }
        
        if (isset($filters['organizer_id'])) {
            $sql .= " AND e.organizer_id = :organizer_id";
            $params[':organizer_id'] = $filters['organizer_id'];
        }
        
        if (isset($filters['search'])) {
            $sql .= " AND (e.title LIKE :search OR e.description LIKE :search)";
            $params[':search'] = "%{$filters['search']}%";
        }
        
        if (isset($filters['is_public'])) {
            $sql .= " AND e.is_public = :is_public";
            $params[':is_public'] = $filters['is_public'];
        }
        
        $sql .= " GROUP BY e.id ORDER BY e.start_at DESC";
        
        if (isset($filters['limit'])) {
            $sql .= " LIMIT " . (int)$filters['limit'];
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Get events by organizer
     */
    public function getByOrganizer($organizerId, $eventStatus = null) {
        $sql = "SELECT e.*, 
                GROUP_CONCAT(DISTINCT c.name) as category_names,
                (SELECT COUNT(*) FROM event_registrations WHERE event_id = e.id AND status = 'registered') as registration_count
                FROM events e
                LEFT JOIN event_categories ec ON e.id = ec.event_id
                LEFT JOIN categories c ON ec.category_id = c.id
                WHERE e.organizer_id = :organizer_id";
        
        $params = [':organizer_id' => $organizerId];
        
        if ($eventStatus) {
            $sql .= " AND e.event_status = :event_status";
            $params[':event_status'] = $eventStatus;
        }
        
        $sql .= " GROUP BY e.id ORDER BY e.start_at DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Register for event
     */
    public function register($eventId, $userId) {
        $sql = "INSERT INTO event_registrations (event_id, user_id, status, registered_at) 
                VALUES (:event_id, :user_id, 'registered', NOW())";
        
        return $this->db->execute($sql, [
            ':event_id' => $eventId,
            ':user_id' => $userId
        ]);
    }
    
    /**
     * Mark participation (attended)
     */
    public function markParticipation($eventId, $userId) {
        $sql = "UPDATE event_registrations 
                SET status = 'attended', attended_at = NOW() 
                WHERE event_id = :event_id AND user_id = :user_id";
        
        return $this->db->execute($sql, [
            ':event_id' => $eventId,
            ':user_id' => $userId
        ]);
    }
    
    /**
     * Unregister from event
     */
    public function unregister($eventId, $userId) {
        $sql = "UPDATE event_registrations 
                SET status = 'cancelled', cancelled_at = NOW() 
                WHERE event_id = :event_id AND user_id = :user_id AND status = 'registered'";
        
        return $this->db->execute($sql, [
            ':event_id' => $eventId,
            ':user_id' => $userId
        ]);
    }
    
    /**
     * Check if user is registered
     */
    public function isRegistered($eventId, $userId) {
        $sql = "SELECT COUNT(*) as count FROM event_registrations 
                WHERE event_id = :event_id AND user_id = :user_id";
        
        $result = $this->db->fetchOne($sql, [
            ':event_id' => $eventId,
            ':user_id' => $userId
        ]);
        
        return $result['count'] > 0;
    }
    
    /**
     * Get user's registered events
     */
    public function getUserEvents($userId, $status = null) {
        $sql = "SELECT e.*, 
                GROUP_CONCAT(DISTINCT c.name) as category_names,
                GROUP_CONCAT(DISTINCT c.color) as category_colors,
                u.full_name as organizer_name, 
                er.status as registration_status,
                er.attended_at
                FROM events e
                INNER JOIN event_registrations er ON e.id = er.event_id
                LEFT JOIN event_categories ec ON e.id = ec.event_id
                LEFT JOIN categories c ON ec.category_id = c.id
                LEFT JOIN users u ON e.organizer_id = u.id
                WHERE er.user_id = :user_id";
        
        $params = [':user_id' => $userId];
        
        if ($status) {
            $sql .= " AND er.status = :status";
            $params[':status'] = $status;
        }
        
        $sql .= " GROUP BY e.id ORDER BY e.start_at DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Approve event (admin)
     */
    public function approve($id, $adminId) {
        $sql = "UPDATE events 
                SET status = 'approved', approved_at = NOW(), approved_by = :admin_id 
                WHERE id = :id";
        
        return $this->db->execute($sql, [
            ':id' => $id,
            ':admin_id' => $adminId
        ]);
    }
    
    /**
     * Reject event (admin)
     */
    public function reject($id) {
        $sql = "UPDATE events SET status = 'rejected' WHERE id = :id";
        return $this->db->execute($sql, [':id' => $id]);
    }
    
    /**
     * Update event status (upcoming/ongoing/completed)
     */
    public function updateEventStatus($id, $eventStatus) {
        $sql = "UPDATE events SET event_status = :event_status WHERE id = :id";
        return $this->db->execute($sql, [
            ':id' => $id,
            ':event_status' => $eventStatus
        ]);
    }
    
    /**
     * Add video to event
     */
    public function addVideo($eventId, $videoUrl) {
        $sql = "UPDATE events SET video_url = :video_url WHERE id = :event_id";
        return $this->db->execute($sql, [
            ':event_id' => $eventId,
            ':video_url' => $videoUrl
        ]);
    }
    
    /**
     * Track event view
     */
    public function trackView($eventId, $userId = null) {
        $sql = "INSERT INTO event_views (event_id, user_id, viewed_at) 
                VALUES (:event_id, :user_id, NOW())";
        
        return $this->db->execute($sql, [
            ':event_id' => $eventId,
            ':user_id' => $userId
        ]);
    }
    
    /**
     * Get recommended events for user based on interests
     */
    public function getRecommended($userId, $limit = 10) {
        $sql = "SELECT DISTINCT e.*, 
                GROUP_CONCAT(DISTINCT c.name) as category_names,
                u.full_name as organizer_name,
                (SELECT COUNT(*) FROM event_registrations WHERE event_id = e.id AND status = 'registered') as registration_count
                FROM events e
                INNER JOIN event_categories ec ON e.id = ec.event_id
                INNER JOIN user_interests ui ON ec.category_id = ui.category_id
                LEFT JOIN categories c ON ec.category_id = c.id
                LEFT JOIN users u ON e.organizer_id = u.id
                WHERE ui.user_id = :user_id 
                AND e.status = 'approved'
                AND e.event_status = 'upcoming'
                AND e.start_at > NOW()
                GROUP BY e.id
                ORDER BY e.start_at ASC
                LIMIT :limit";
        
        return $this->db->fetchAll($sql, [
            ':user_id' => $userId,
            ':limit' => $limit
        ]);
    }
    
    /**
     * Get events from followed organizers
     */
    public function getFromFollowing($userId, $limit = 20) {
        $sql = "SELECT DISTINCT e.*, 
                GROUP_CONCAT(DISTINCT c.name) as category_names,
                u.full_name as organizer_name,
                u.avatar_url as organizer_avatar,
                (SELECT COUNT(*) FROM event_registrations WHERE event_id = e.id AND status = 'registered') as registration_count
                FROM events e
                INNER JOIN follows f ON e.organizer_id = f.following_id
                LEFT JOIN event_categories ec ON e.id = ec.event_id
                LEFT JOIN categories c ON ec.category_id = c.id
                LEFT JOIN users u ON e.organizer_id = u.id
                WHERE f.follower_id = :user_id
                AND e.status = 'approved'
                GROUP BY e.id
                ORDER BY e.created_at DESC
                LIMIT :limit";
        
        return $this->db->fetchAll($sql, [
            ':user_id' => $userId,
            ':limit' => $limit
        ]);
    }
    
    /**
     * Get ongoing events with videos (for Lives feature)
     */
    public function getOngoingWithVideos($userId = null) {
        $sql = "SELECT DISTINCT e.*, 
                u.full_name as organizer_name,
                u.avatar_url as organizer_avatar
                FROM events e
                LEFT JOIN users u ON e.organizer_id = u.id
                WHERE e.event_status = 'ongoing' 
                AND e.video_url IS NOT NULL
                AND e.status = 'approved'";
        
        if ($userId) {
            $sql .= " AND e.organizer_id IN (
                        SELECT following_id FROM follows WHERE follower_id = :user_id
                     )";
            return $this->db->fetchAll($sql, [':user_id' => $userId]);
        }
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Search events
     */
    public function search($query, $filters = []) {
        $sql = "SELECT DISTINCT e.*, 
                GROUP_CONCAT(DISTINCT c.name) as category_names,
                u.full_name as organizer_name,
                (SELECT COUNT(*) FROM event_registrations WHERE event_id = e.id AND status = 'registered') as registration_count
                FROM events e
                LEFT JOIN event_categories ec ON e.id = ec.event_id
                LEFT JOIN categories c ON ec.category_id = c.id
                LEFT JOIN users u ON e.organizer_id = u.id
                WHERE e.status = 'approved'
                AND (e.title LIKE :query OR e.description LIKE :query OR c.name LIKE :query)";
        
        $params = [':query' => "%$query%"];
        
        if (isset($filters['category_id'])) {
            $sql .= " AND ec.category_id = :category_id";
            $params[':category_id'] = $filters['category_id'];
        }
        
        if (isset($filters['date_from'])) {
            $sql .= " AND e.start_at >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }
        
        if (isset($filters['date_to'])) {
            $sql .= " AND e.start_at <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }
        
        $sql .= " GROUP BY e.id ORDER BY e.start_at ASC";
        
        if (isset($filters['limit'])) {
            $sql .= " LIMIT " . (int)$filters['limit'];
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Get total event count
     */
    public function getEventCount() {
        $sql = "SELECT COUNT(*) as count FROM events WHERE status = 'approved'";
        $result = $this->db->fetchOne($sql);
        return $result['count'] ?? 0;
    }
}
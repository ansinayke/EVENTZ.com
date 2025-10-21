<?php
/**
 * Sponsorship Model - FIXED VERSION
 * Handles all sponsorship-related database operations
 */

class Sponsorship {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Create sponsorship plan
     */
    public function createPlan($data) {
        $sql = "INSERT INTO sponsorship_plans (sponsor_id, plan_name, description, price, 
                benefits, duration_days, is_active, created_at, updated_at) 
                VALUES (:sponsor_id, :plan_name, :description, :price, :benefits, :duration_days, 
                :is_active, NOW(), NOW())";
        
        $params = [
            ':sponsor_id' => $data['sponsor_id'],
            ':plan_name' => $data['plan_name'],
            ':description' => $data['description'] ?? null,
            ':price' => $data['amount'] ?? $data['price'], // Support both field names
            ':benefits' => $data['benefits'] ?? null,
            ':duration_days' => $data['duration_days'] ?? 30,
            ':is_active' => $data['is_active'] ?? true
        ];
        
        if ($this->db->execute($sql, $params)) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Update sponsorship plan
     */
    public function updatePlan($id, $data) {
        $fields = [];
        $params = [':id' => $id];
        
        $allowedFields = ['plan_name', 'description', 'price', 'benefits', 'duration_days', 'is_active'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = :$field";
                $params[":$field"] = $data[$field];
            }
        }
        
        // Support 'amount' as alias for 'price'
        if (isset($data['amount']) && !isset($data['price'])) {
            $fields[] = "price = :price";
            $params[":price"] = $data['amount'];
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $sql = "UPDATE sponsorship_plans SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = :id";
        
        return $this->db->execute($sql, $params);
    }
    
    /**
     * Delete sponsorship plan
     */
    public function deletePlan($id) {
        $sql = "DELETE FROM sponsorship_plans WHERE id = :id";
        return $this->db->execute($sql, [':id' => $id]);
    }
    
    /**
     * Get plan by ID
     */
    public function getPlanById($id) {
        $sql = "SELECT sp.*, u.full_name as sponsor_name, u.avatar_url as sponsor_avatar
                FROM sponsorship_plans sp
                LEFT JOIN users u ON sp.sponsor_id = u.id
                WHERE sp.id = :id";
        
        return $this->db->fetchOne($sql, [':id' => $id]);
    }
    
    /**
     * Get plans by sponsor
     */
    public function getPlansBySponsor($sponsorId) {
        $sql = "SELECT * FROM sponsorship_plans 
                WHERE sponsor_id = :sponsor_id 
                ORDER BY price DESC";
        
        return $this->db->fetchAll($sql, [':sponsor_id' => $sponsorId]);
    }
    
    /**
     * Get all active plans
     */
    public function getAllActivePlans() {
        $sql = "SELECT sp.*, u.full_name as sponsor_name, u.avatar_url as sponsor_avatar
                FROM sponsorship_plans sp
                LEFT JOIN users u ON sp.sponsor_id = u.id
                WHERE sp.is_active = 1
                ORDER BY sp.price DESC";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Create sponsorship agreement
     */
    public function createAgreement($data) {
        $sql = "INSERT INTO event_sponsorships (event_id, sponsor_id, plan_id, amount, status, created_at) 
                VALUES (:event_id, :sponsor_id, :plan_id, :amount, :status, NOW())";
        
        $params = [
            ':event_id' => $data['event_id'],
            ':sponsor_id' => $data['sponsor_id'],
            ':plan_id' => $data['plan_id'] ?? null,
            ':amount' => $data['amount'],
            ':status' => $data['status'] ?? 'pending'
        ];
        
        if ($this->db->execute($sql, $params)) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Update sponsorship status
     */
    public function updateAgreementStatus($id, $status) {
        $sql = "UPDATE event_sponsorships SET status = :status";
        
        if ($status === 'approved') {
            $sql .= ", approved_at = NOW()";
        }
        
        $sql .= " WHERE id = :id";
        
        return $this->db->execute($sql, [
            ':id' => $id,
            ':status' => $status
        ]);
    }
    
    /**
     * Get event sponsorships
     */
    public function getEventSponsorships($eventId) {
        $sql = "SELECT es.*, u.full_name as sponsor_name, u.avatar_url as sponsor_avatar,
                sp.plan_name
                FROM event_sponsorships es
                LEFT JOIN users u ON es.sponsor_id = u.id
                LEFT JOIN sponsorship_plans sp ON es.plan_id = sp.id
                WHERE es.event_id = :event_id
                ORDER BY es.created_at DESC";
        
        return $this->db->fetchAll($sql, [':event_id' => $eventId]);
    }
    
    /**
     * Get sponsor's sponsorships
     */
    public function getSponsorSponsorships($sponsorId) {
        $sql = "SELECT es.*, e.title as event_title, e.start_at as event_date, 
                e.banner_url as event_banner, e.event_status
                FROM event_sponsorships es
                LEFT JOIN events e ON es.event_id = e.id
                WHERE es.sponsor_id = :sponsor_id
                ORDER BY es.created_at DESC";
        
        return $this->db->fetchAll($sql, [':sponsor_id' => $sponsorId]);
    }
    
    /**
     * Get sponsorship statistics
     */
    public function getSponsorStats($sponsorId) {
        $sql = "SELECT 
                COUNT(*) as total_sponsorships,
                SUM(amount) as total_amount,
                COUNT(CASE WHEN status = 'approved' THEN 1 END) as active_sponsorships,
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_sponsorships,
                (SELECT COUNT(DISTINCT event_id) FROM event_sponsorships WHERE sponsor_id = :sponsor_id) as events_sponsored
                FROM event_sponsorships
                WHERE sponsor_id = :sponsor_id";
        
        $result = $this->db->fetchOne($sql, [':sponsor_id' => $sponsorId]);
        
        // Ensure all values are set
        return [
            'total_sponsorships' => $result['total_sponsorships'] ?? 0,
            'total_amount' => $result['total_amount'] ?? 0,
            'active_sponsorships' => $result['active_sponsorships'] ?? 0,
            'completed_sponsorships' => $result['completed_sponsorships'] ?? 0,
            'events_sponsored' => $result['events_sponsored'] ?? 0
        ];
    }
}
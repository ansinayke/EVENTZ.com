<?php
/**
 * User Model
 * Handles all user-related database operations
 */

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Create a new user
     */
    public function create($data) {
        $sql = "INSERT INTO users (full_name, email, password_hash, phone, bio, location) 
                VALUES (:full_name, :email, :password_hash, :phone, :bio, :location)";
        
        $params = [
            ':full_name' => $data['full_name'],
            ':email' => $data['email'],
            ':password_hash' => password_hash($data['password'], HASH_ALGO),
            ':phone' => $data['phone'] ?? null,
            ':bio' => $data['bio'] ?? null,
            ':location' => $data['location'] ?? null
        ];
        
        if ($this->db->execute($sql, $params)) {
            $userId = $this->db->lastInsertId();
            
            // Assign role
            if (isset($data['role'])) {
                $this->assignRole($userId, $data['role']);
            }
            
            // Add interests if provided
            if (isset($data['interests']) && is_array($data['interests'])) {
                $this->addInterests($userId, $data['interests']);
            }
            
            return $userId;
        }
        
        return false;
    }
    
    /**
     * Find user by email
     */
    public function findByEmail($email) {
        $sql = "SELECT u.*, GROUP_CONCAT(r.name) as roles 
                FROM users u 
                LEFT JOIN user_roles ur ON u.id = ur.user_id 
                LEFT JOIN roles r ON ur.role_id = r.id 
                WHERE u.email = :email 
                GROUP BY u.id";
        
        return $this->db->fetchOne($sql, [':email' => $email]);
    }
    
    /**
     * Find user by ID
     */
    public function findById($id) {
        $sql = "SELECT u.*, GROUP_CONCAT(r.name) as roles 
                FROM users u 
                LEFT JOIN user_roles ur ON u.id = ur.user_id 
                LEFT JOIN roles r ON ur.role_id = r.id 
                WHERE u.id = :id 
                GROUP BY u.id";
        
        return $this->db->fetchOne($sql, [':id' => $id]);
    }
    
    /**
     * Update user profile
     */
    public function update($id, $data) {
        $fields = [];
        $params = [':id' => $id];
        
        $allowedFields = ['full_name', 'phone', 'bio', 'website', 'location', 'avatar_url'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = :$field";
                $params[":$field"] = $data[$field];
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        
        return $this->db->execute($sql, $params);
    }
    
    /**
     * Assign role to user
     */
    public function assignRole($userId, $roleName) {
        $sql = "INSERT INTO user_roles (user_id, role_id) 
                SELECT :user_id, id FROM roles WHERE name = :role_name";
        
        return $this->db->execute($sql, [
            ':user_id' => $userId,
            ':role_name' => $roleName
        ]);
    }
    
    /**
     * Get user roles
     */
    public function getRoles($userId) {
        $sql = "SELECT r.name FROM roles r 
                INNER JOIN user_roles ur ON r.id = ur.role_id 
                WHERE ur.user_id = :user_id";
        
        $roles = $this->db->fetchAll($sql, [':user_id' => $userId]);
        return array_column($roles, 'name');
    }
    
    /**
     * Check if user has role
     */
    public function hasRole($userId, $roleName) {
        $roles = $this->getRoles($userId);
        return in_array($roleName, $roles);
    }
    
    /**
     * Add user interests
     */
    public function addInterests($userId, $categoryIds) {
        $sql = "INSERT IGNORE INTO user_interests (user_id, category_id) VALUES (:user_id, :category_id)";
        
        foreach ($categoryIds as $categoryId) {
            $this->db->execute($sql, [
                ':user_id' => $userId,
                ':category_id' => $categoryId
            ]);
        }
        
        return true;
    }
    
    /**
     * Get user interests
     */
    public function getInterests($userId) {
        $sql = "SELECT c.* FROM categories c 
                INNER JOIN user_interests ui ON c.id = ui.category_id 
                WHERE ui.user_id = :user_id";
        
        return $this->db->fetchAll($sql, [':user_id' => $userId]);
    }
    
    /**
     * Search users
     */
    public function search($query, $role = null) {
        $sql = "SELECT DISTINCT u.* FROM users u 
                LEFT JOIN user_roles ur ON u.id = ur.user_id 
                LEFT JOIN roles r ON ur.role_id = r.id 
                WHERE (u.full_name LIKE :query OR u.email LIKE :query)";
        
        $params = [':query' => "%$query%"];
        
        if ($role) {
            $sql .= " AND r.name = :role";
            $params[':role'] = $role;
        }
        
        $sql .= " LIMIT 20";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Follow a user
     */
    public function follow($followerId, $followingId) {
        $sql = "INSERT IGNORE INTO follows (follower_id, following_id) VALUES (:follower_id, :following_id)";
        
        return $this->db->execute($sql, [
            ':follower_id' => $followerId,
            ':following_id' => $followingId
        ]);
    }
    
    /**
     * Unfollow a user
     */
    public function unfollow($followerId, $followingId) {
        $sql = "DELETE FROM follows WHERE follower_id = :follower_id AND following_id = :following_id";
        
        return $this->db->execute($sql, [
            ':follower_id' => $followerId,
            ':following_id' => $followingId
        ]);
    }
    
    /**
     * Check if following
     */
    public function isFollowing($followerId, $followingId) {
        $sql = "SELECT COUNT(*) as count FROM follows 
                WHERE follower_id = :follower_id AND following_id = :following_id";
        
        $result = $this->db->fetchOne($sql, [
            ':follower_id' => $followerId,
            ':following_id' => $followingId
        ]);
        
        return $result['count'] > 0;
    }
    
    /**
     * Get followers
     */
    public function getFollowers($userId) {
        $sql = "SELECT u.* FROM users u 
                INNER JOIN follows f ON u.id = f.follower_id 
                WHERE f.following_id = :user_id";
        
        return $this->db->fetchAll($sql, [':user_id' => $userId]);
    }
    
    /**
     * Get following
     */
    public function getFollowing($userId) {
        $sql = "SELECT u.* FROM users u 
                INNER JOIN follows f ON u.id = f.following_id 
                WHERE f.follower_id = :user_id";
        
        return $this->db->fetchAll($sql, [':user_id' => $userId]);
    }
    
    /**
     * Get follower count
     */
    public function getFollowerCount($userId) {
        $sql = "SELECT COUNT(*) as count FROM follows WHERE following_id = :user_id";
        $result = $this->db->fetchOne($sql, [':user_id' => $userId]);
        return $result['count'] ?? 0;
    }
    
    /**
     * Get following count
     */
    public function getFollowingCount($userId) {
        $sql = "SELECT COUNT(*) as count FROM follows WHERE follower_id = :user_id";
        $result = $this->db->fetchOne($sql, [':user_id' => $userId]);
        return $result['count'] ?? 0;
    }
    
    /**
     * Update last login
     */
    public function updateLastLogin($userId) {
        $sql = "UPDATE users SET last_login = NOW() WHERE id = :id";
        return $this->db->execute($sql, [':id' => $userId]);
    }
    
    /**
     * Get total user count
     */
    public function getUserCount() {
        $sql = "SELECT COUNT(*) as count FROM users";
        $result = $this->db->fetchOne($sql);
        return $result['count'] ?? 0;
    }
    
    /**
     * Get user count by role
     */
    public function getUserCountByRole($roleName) {
        $sql = "SELECT COUNT(*) as count 
                FROM users u 
                INNER JOIN user_roles ur ON u.id = ur.user_id 
                INNER JOIN roles r ON ur.role_id = r.id 
                WHERE r.name = :role_name";
        $result = $this->db->fetchOne($sql, [':role_name' => $roleName]);
        return $result['count'] ?? 0;
    }
}
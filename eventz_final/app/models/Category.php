<?php
/**
 * Category Model
 * Handles all category-related database operations
 */

class Category {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get all categories
     */
    public function getAll() {
        $sql = "SELECT * FROM categories ORDER BY name ASC";
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Get category by ID
     */
    public function findById($id) {
        $sql = "SELECT * FROM categories WHERE id = :id";
        return $this->db->fetchOne($sql, [':id' => $id]);
    }
    
    /**
     * Get category by slug
     */
    public function findBySlug($slug) {
        $sql = "SELECT * FROM categories WHERE slug = :slug";
        return $this->db->fetchOne($sql, [':slug' => $slug]);
    }
}
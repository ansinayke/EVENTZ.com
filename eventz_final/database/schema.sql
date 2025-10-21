-- =====================================================
-- EVENTZ - Complete Database Schema
-- Event Management Platform with Full Functionality
-- =====================================================

-- Drop existing database if needed (use with caution)
-- DROP DATABASE IF EXISTS eventz;
CREATE DATABASE IF NOT EXISTS eventz CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE eventz;

-- =====================================================
-- 1) CORE USER MANAGEMENT
-- =====================================================

-- Table: users
CREATE TABLE IF NOT EXISTS users (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    avatar_url VARCHAR(255),
    bio TEXT,
    website VARCHAR(255),
    location VARCHAR(100),
    status VARCHAR(20) DEFAULT 'active', -- active, blocked, suspended
    email_verified BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login DATETIME,
    INDEX idx_email (email),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- Table: roles
CREATE TABLE IF NOT EXISTS roles (
    id TINYINT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(30) NOT NULL UNIQUE, -- organizer, participant, sponsor, supplier, admin
    description VARCHAR(255)
) ENGINE=InnoDB;

-- Insert default roles
INSERT INTO roles (name, description) VALUES
('admin', 'System administrator with full access'),
('organizer', 'Event organizer who can create and manage events'),
('participant', 'Event participant who can register and attend events'),
('sponsor', 'Sponsor who can provide financial support to events'),
('supplier', 'Supplier who can provide resources and services')
ON DUPLICATE KEY UPDATE description=VALUES(description);

-- Table: user_roles (many-to-many between users and roles)
CREATE TABLE IF NOT EXISTS user_roles (
    user_id BIGINT NOT NULL,
    role_id TINYINT NOT NULL,
    assigned_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, role_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    INDEX idx_user_role (user_id, role_id)
) ENGINE=InnoDB;

-- =====================================================
-- 2) CATEGORIES & INTERESTS
-- =====================================================

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    icon VARCHAR(50),
    color VARCHAR(7),
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Insert default categories
INSERT INTO categories (name, slug, icon, color) VALUES
('Music & Concerts', 'music', 'fa-music', '#FF6B6B'),
('Sports & Fitness', 'sports', 'fa-running', '#4ECDC4'),
('Technology & Innovation', 'technology', 'fa-laptop-code', '#45B7D1'),
('Education & Workshops', 'education', 'fa-graduation-cap', '#FFA07A'),
('Business & Networking', 'business', 'fa-briefcase', '#98D8C8'),
('Charity & Fundraising', 'charity', 'fa-hand-holding-heart', '#F7DC6F'),
('Art & Culture', 'art', 'fa-palette', '#BB8FCE'),
('Fashion & Lifestyle', 'fashion', 'fa-tshirt', '#F8B4D9'),
('Food & Drink', 'food', 'fa-utensils', '#FFB6C1'),
('Gaming & Esports', 'gaming', 'fa-gamepad', '#9B59B6'),
('Health & Wellness', 'health', 'fa-heartbeat', '#52C41A'),
('Travel & Adventure', 'travel', 'fa-plane', '#1890FF'),
('Community & Social', 'community', 'fa-users', '#FA8C16'),
('Festivals & Fairs', 'festivals', 'fa-flag', '#EB2F96'),
('Theatre & Performing Arts', 'theatre', 'fa-theater-masks', '#722ED1')
ON DUPLICATE KEY UPDATE icon=VALUES(icon), color=VALUES(color);

-- Table: user_interests (user's preferred categories)
CREATE TABLE IF NOT EXISTS user_interests (
    user_id BIGINT NOT NULL,
    category_id INT NOT NULL,
    selected_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, category_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- 3) EVENTS MANAGEMENT
-- =====================================================

CREATE TABLE IF NOT EXISTS events (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    organizer_id BIGINT NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    location_text VARCHAR(255),
    venue_name VARCHAR(150),
    address TEXT,
    city VARCHAR(100),
    country VARCHAR(100),
    start_at DATETIME NOT NULL,
    end_at DATETIME NOT NULL,
    capacity INT DEFAULT 0,
    status VARCHAR(20) DEFAULT 'pending', -- pending, approved, rejected, cancelled
    event_status VARCHAR(20) DEFAULT 'upcoming', -- upcoming, ongoing, completed
    banner_url VARCHAR(255),
    video_url VARCHAR(255), -- For ongoing events
    is_featured BOOLEAN DEFAULT FALSE,
    is_public BOOLEAN DEFAULT TRUE,
    registration_deadline DATETIME,
    view_count INT DEFAULT 0,
    registration_count INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    approved_at DATETIME,
    approved_by BIGINT,
    FOREIGN KEY (organizer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_organizer (organizer_id),
    INDEX idx_status (status, event_status),
    INDEX idx_dates (start_at, end_at),
    INDEX idx_featured (is_featured, status)
) ENGINE=InnoDB;

-- Table: event_categories (many-to-many)
CREATE TABLE IF NOT EXISTS event_categories (
    event_id BIGINT NOT NULL,
    category_id INT NOT NULL,
    PRIMARY KEY (event_id, category_id),
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table: event_media (additional images/videos for events)
CREATE TABLE IF NOT EXISTS event_media (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    event_id BIGINT NOT NULL,
    media_type VARCHAR(20), -- image, video
    media_url VARCHAR(255) NOT NULL,
    caption TEXT,
    display_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    INDEX idx_event_media (event_id, display_order)
) ENGINE=InnoDB;

-- =====================================================
-- 4) EVENT PARTICIPATION
-- =====================================================

CREATE TABLE IF NOT EXISTS event_registrations (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    event_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    status VARCHAR(20) DEFAULT 'registered', -- registered, attended, cancelled, no_show
    ticket_type VARCHAR(50),
    price_paid DECIMAL(10,2) DEFAULT 0.00,
    qr_code VARCHAR(100),
    registered_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    attended_at DATETIME,
    cancelled_at DATETIME,
    notes TEXT,
    UNIQUE KEY unique_registration (event_id, user_id),
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_registrations (user_id, status),
    INDEX idx_event_registrations (event_id, status)
) ENGINE=InnoDB;

-- Table: event_views (track who viewed events)
CREATE TABLE IF NOT EXISTS event_views (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    event_id BIGINT NOT NULL,
    user_id BIGINT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    viewed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_event_views (event_id, viewed_at),
    INDEX idx_user_views (user_id, viewed_at)
) ENGINE=InnoDB;

-- =====================================================
-- 5) SOCIAL FEATURES - FOLLOWING SYSTEM
-- =====================================================

CREATE TABLE IF NOT EXISTS follows (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    follower_id BIGINT NOT NULL, -- User who is following
    following_id BIGINT NOT NULL, -- User being followed
    followed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_follow (follower_id, following_id),
    FOREIGN KEY (follower_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (following_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_follower (follower_id),
    INDEX idx_following (following_id),
    CHECK (follower_id != following_id)
) ENGINE=InnoDB;

-- =====================================================
-- 6) SPONSORSHIP SYSTEM
-- =====================================================

-- Table: sponsorship_plans (created by sponsors)
CREATE TABLE IF NOT EXISTS sponsorship_plans (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    sponsor_id BIGINT NOT NULL,
    plan_name VARCHAR(100) NOT NULL,
    description TEXT,
    benefits TEXT,
    price DECIMAL(10,2) NOT NULL,
    duration_days INT, -- How long the sponsorship lasts
    is_active BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sponsor_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_sponsor_plans (sponsor_id, is_active)
) ENGINE=InnoDB;

-- Table: event_sponsorships (sponsors sponsor specific events)
CREATE TABLE IF NOT EXISTS event_sponsorships (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    event_id BIGINT NOT NULL,
    sponsor_id BIGINT NOT NULL,
    plan_id BIGINT,
    amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(20) DEFAULT 'pending', -- pending, approved, rejected, completed
    agreement_url VARCHAR(255),
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    approved_at DATETIME,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (sponsor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES sponsorship_plans(id) ON DELETE SET NULL,
    INDEX idx_event_sponsorships (event_id, status),
    INDEX idx_sponsor_sponsorships (sponsor_id, status)
) ENGINE=InnoDB;

-- =====================================================
-- 7) ADMIN & MODERATION
-- =====================================================

CREATE TABLE IF NOT EXISTS admin_reviews (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    event_id BIGINT NOT NULL,
    admin_id BIGINT NOT NULL,
    status VARCHAR(20) NOT NULL, -- approved, rejected
    reason TEXT,
    reviewed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_admin_reviews (admin_id, reviewed_at)
) ENGINE=InnoDB;

-- =====================================================
-- 8) NOTIFICATIONS SYSTEM
-- =====================================================

CREATE TABLE IF NOT EXISTS notifications (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    type VARCHAR(40) NOT NULL, -- event_approved, new_follower, event_reminder, etc.
    title VARCHAR(120) NOT NULL,
    body VARCHAR(255),
    url VARCHAR(255),
    related_id BIGINT, -- ID of related entity (event, user, etc.)
    is_read BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_notifications (user_id, is_read, created_at)
) ENGINE=InnoDB;

-- =====================================================
-- 9) USER PORTFOLIOS
-- =====================================================

CREATE TABLE IF NOT EXISTS user_portfolios (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    title VARCHAR(120),
    summary TEXT,
    skills TEXT, -- JSON or comma-separated
    achievements TEXT,
    is_public BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_portfolio (user_id)
) ENGINE=InnoDB;

-- =====================================================
-- 10) SUPPLIER SYSTEM (Optional - for future use)
-- =====================================================

CREATE TABLE IF NOT EXISTS supplier_items (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    supplier_id BIGINT NOT NULL,
    name VARCHAR(120) NOT NULL,
    description TEXT,
    category VARCHAR(80),
    unit VARCHAR(20),
    price_per_unit DECIMAL(10,2),
    available_qty INT DEFAULT 0,
    availability_status VARCHAR(20) DEFAULT 'available',
    image_url VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (supplier_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_supplier_items (supplier_id, availability_status)
) ENGINE=InnoDB;

-- =====================================================
-- 11) USER SETTINGS
-- =====================================================

CREATE TABLE IF NOT EXISTS user_settings (
    user_id BIGINT PRIMARY KEY,
    theme VARCHAR(20) DEFAULT 'light', -- light, dark
    language VARCHAR(10) DEFAULT 'en',
    email_notifications BOOLEAN DEFAULT TRUE,
    push_notifications BOOLEAN DEFAULT TRUE,
    show_email BOOLEAN DEFAULT FALSE,
    show_phone BOOLEAN DEFAULT FALSE,
    profile_visibility VARCHAR(20) DEFAULT 'public', -- public, private, followers
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- 12) ACTIVITY LOG (Optional - for analytics)
-- =====================================================

CREATE TABLE IF NOT EXISTS activity_logs (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT,
    action VARCHAR(50) NOT NULL, -- login, logout, create_event, register_event, etc.
    entity_type VARCHAR(50), -- event, user, sponsorship, etc.
    entity_id BIGINT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_activity_logs (user_id, created_at),
    INDEX idx_entity_logs (entity_type, entity_id)
) ENGINE=InnoDB;

-- =====================================================
-- TRIGGERS FOR AUTO-STATUS UPDATES
-- =====================================================

DELIMITER //

-- Trigger to update event_status based on dates
CREATE TRIGGER update_event_status_before_update
BEFORE UPDATE ON events
FOR EACH ROW
BEGIN
    IF NEW.status = 'approved' THEN
        IF NEW.start_at > NOW() THEN
            SET NEW.event_status = 'upcoming';
        ELSEIF NEW.start_at <= NOW() AND NEW.end_at >= NOW() THEN
            SET NEW.event_status = 'ongoing';
        ELSEIF NEW.end_at < NOW() THEN
            SET NEW.event_status = 'completed';
        END IF;
    END IF;
END//

-- Trigger to create default user settings
CREATE TRIGGER create_user_settings_after_insert
AFTER INSERT ON users
FOR EACH ROW
BEGIN
    INSERT INTO user_settings (user_id) VALUES (NEW.id);
END//

-- Trigger to update event view count
CREATE TRIGGER update_event_view_count
AFTER INSERT ON event_views
FOR EACH ROW
BEGIN
    UPDATE events SET view_count = view_count + 1 WHERE id = NEW.event_id;
END//

-- Trigger to update event registration count
CREATE TRIGGER update_event_registration_count_insert
AFTER INSERT ON event_registrations
FOR EACH ROW
BEGIN
    IF NEW.status = 'registered' THEN
        UPDATE events SET registration_count = registration_count + 1 WHERE id = NEW.event_id;
    END IF;
END//

CREATE TRIGGER update_event_registration_count_update
AFTER UPDATE ON event_registrations
FOR EACH ROW
BEGIN
    IF OLD.status != 'registered' AND NEW.status = 'registered' THEN
        UPDATE events SET registration_count = registration_count + 1 WHERE id = NEW.event_id;
    ELSEIF OLD.status = 'registered' AND NEW.status != 'registered' THEN
        UPDATE events SET registration_count = registration_count - 1 WHERE id = NEW.event_id;
    END IF;
END//

DELIMITER ;

-- =====================================================
-- INDEXES FOR PERFORMANCE
-- =====================================================

CREATE INDEX idx_events_search ON events(title, description(100));
CREATE INDEX idx_users_search ON users(full_name, email);
CREATE INDEX idx_events_organizer_status ON events(organizer_id, status, event_status);

-- =====================================================
-- VIEWS FOR COMMON QUERIES
-- =====================================================

-- View: Popular events
CREATE OR REPLACE VIEW popular_events AS
SELECT 
    e.*,
    u.full_name as organizer_name,
    u.avatar_url as organizer_avatar,
    COUNT(DISTINCT er.id) as total_registrations,
    COUNT(DISTINCT ev.id) as total_views
FROM events e
LEFT JOIN users u ON e.organizer_id = u.id
LEFT JOIN event_registrations er ON e.id = er.event_id AND er.status = 'registered'
LEFT JOIN event_views ev ON e.id = ev.event_id
WHERE e.status = 'approved'
GROUP BY e.id
ORDER BY total_registrations DESC, total_views DESC;

-- View: User statistics
CREATE OR REPLACE VIEW user_statistics AS
SELECT 
    u.id,
    u.full_name,
    u.email,
    u.avatar_url,
    COUNT(DISTINCT CASE WHEN ur.role_id = 2 THEN e.id END) as organized_events,
    COUNT(DISTINCT CASE WHEN ur.role_id = 3 THEN er.id END) as participated_events,
    COUNT(DISTINCT f1.follower_id) as followers_count,
    COUNT(DISTINCT f2.following_id) as following_count
FROM users u
LEFT JOIN user_roles ur ON u.id = ur.user_id
LEFT JOIN events e ON u.id = e.organizer_id AND ur.role_id = 2
LEFT JOIN event_registrations er ON u.id = er.user_id AND ur.role_id = 3
LEFT JOIN follows f1 ON u.id = f1.following_id
LEFT JOIN follows f2 ON u.id = f2.follower_id
GROUP BY u.id;

-- =====================================================
-- SAMPLE DATA (Optional - for testing)
-- =====================================================

-- Create admin user (password: admin123)
INSERT INTO users (full_name, email, password_hash, status, email_verified) VALUES
('System Admin', 'admin@eventz.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'active', TRUE);

-- Assign admin role
INSERT INTO user_roles (user_id, role_id) 
SELECT id, 1 FROM users WHERE email = 'admin@eventz.com';

-- =====================================================
-- COMPLETION MESSAGE
-- =====================================================

SELECT 'Database schema created successfully!' as message;
SELECT 'Total tables created: ' as info, COUNT(*) as count FROM information_schema.tables WHERE table_schema = 'eventz';
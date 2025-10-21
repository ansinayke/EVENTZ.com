<?php
/**
 * EVENTZ - Configuration File
 * All application settings and constants
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'eventz');

// Application Settings
define('APP_NAME', 'EVENTZ');
define('BASE_URL', 'http://localhost');
define('BASE_PATH', '/eventz_final');
define('FULL_URL', BASE_URL . BASE_PATH);

// File Upload Settings
define('UPLOAD_PATH', __DIR__ . '/../public/uploads/');
define('AVATAR_PATH', UPLOAD_PATH . 'avatars/');
define('EVENT_IMAGE_PATH', UPLOAD_PATH . 'events/');
define('VIDEO_PATH', UPLOAD_PATH . 'videos/');
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('MAX_VIDEO_SIZE', 100 * 1024 * 1024); // 100MB

// Allowed file types
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('ALLOWED_VIDEO_TYPES', ['video/mp4', 'video/webm', 'video/ogg']);

// Session Settings
define('SESSION_LIFETIME', 3600 * 24); // 24 hours

// Pagination
define('ITEMS_PER_PAGE', 12);

// Security
define('PASSWORD_MIN_LENGTH', 6);
define('HASH_ALGO', PASSWORD_DEFAULT);

// Timezone
date_default_timezone_set('UTC');

// Error Reporting (set to 0 in production)
// For AJAX requests, we don't want HTML errors mixed with JSON
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    ini_set('display_errors', 0);
} else {
    ini_set('display_errors', 1);
}
error_reporting(E_ALL);
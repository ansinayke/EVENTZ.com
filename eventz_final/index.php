<?php
/**
 * EVENTZ - Event Management Platform
 * Main Entry Point
 */

// Start session with explicit cookie params for stability
require_once __DIR__ . '/config/config.php';

$cookieParams = [
    'lifetime' => SESSION_LIFETIME,
    'path' => BASE_PATH ?: '/',
    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    'httponly' => true,
    'samesite' => 'Lax'
];

if (PHP_VERSION_ID >= 70300) {
    session_set_cookie_params($cookieParams);
} else {
    session_set_cookie_params(
        $cookieParams['lifetime'],
        $cookieParams['path'].'; samesite='.$cookieParams['samesite'],
        '',
        $cookieParams['secure'],
        $cookieParams['httponly']
    );
}

session_start();

// Error reporting (disable in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Load configuration (already required above for constants)

// Autoloader
spl_autoload_register(function ($className) {
    $paths = [
        __DIR__ . '/app/core/',
        __DIR__ . '/app/controllers/',
        __DIR__ . '/app/models/',
        __DIR__ . '/app/helpers/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Initialize router
$router = new Router();

// Public routes
$router->get('/', function() {
    // Get dynamic stats from database
    require_once __DIR__ . '/app/core/Database.php';
    require_once __DIR__ . '/app/models/User.php';
    require_once __DIR__ . '/app/models/Event.php';
    
    $userModel = new User();
    $eventModel = new Event();
    
    // Get stats
    $stats = [
        'total_events' => $eventModel->getEventCount(),
        'total_members' => $userModel->getUserCount(),
        'total_sponsors' => $userModel->getUserCountByRole('sponsor'),
        'total_organizers' => $userModel->getUserCountByRole('organizer')
    ];
    
    require_once __DIR__ . '/app/views/welcome.php';
});

$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');
$router->get('/logout', 'AuthController@logout');

// Static pages
$router->get('/about', function() {
    require_once __DIR__ . '/app/views/about.php';
});

$router->get('/contact', function() {
    require_once __DIR__ . '/app/views/contact.php';
});

// Participant routes
$router->get('/participant/home', 'ParticipantController@home');
$router->get('/participant/explore', 'ParticipantController@explore');
$router->get('/participant/portfolio', 'ParticipantController@portfolio');
$router->post('/participant/register-event', 'ParticipantController@registerEvent');
$router->post('/participant/unregister-event', 'ParticipantController@unregisterEvent');
$router->post('/participant/mark-participation', 'ParticipantController@markParticipation');
$router->get('/participant/event-details', 'ParticipantController@getEventDetails');

// Organizer routes
$router->get('/organizer/home', 'OrganizerController@home');
$router->get('/organizer/dashboard', 'OrganizerController@dashboard');
$router->get('/organizer/analytics', 'OrganizerController@analytics');
$router->post('/organizer/create-event', 'OrganizerController@createEvent');
$router->post('/organizer/update-event', 'OrganizerController@updateEvent');
$router->post('/organizer/delete-event', 'OrganizerController@deleteEvent');
$router->post('/organizer/upload-video', 'OrganizerController@uploadVideo');
$router->get('/organizer/profile', 'OrganizerController@profile');

// Sponsor routes
$router->get('/sponsor/home', 'SponsorController@home');
$router->get('/sponsor/dashboard', 'SponsorController@dashboard');
$router->get('/sponsor/analytics', 'SponsorController@analytics');
$router->post('/sponsor/create-plan', 'SponsorController@createPlan');
$router->post('/sponsor/update-plan', 'SponsorController@updatePlan');
$router->post('/sponsor/delete-plan', 'SponsorController@deletePlan');
$router->post('/sponsor/sponsor-event', 'SponsorController@sponsorEvent');
$router->get('/sponsor/profile', 'SponsorController@profile');

// Admin routes
$router->get('/admin/dashboard', 'AdminController@dashboard');
$router->get('/admin/analytics', 'AdminController@analytics');
$router->post('/admin/approve-event', 'AdminController@approveEvent');
$router->post('/admin/reject-event', 'AdminController@rejectEvent');
$router->post('/admin/delete-event', 'AdminController@deleteEvent');

// User routes
$router->get('/profile', 'UserController@profile');
$router->post('/profile/update', 'UserController@updateProfile');
$router->get('/settings', 'UserController@settings');
$router->get('/search', 'UserController@search');
$router->post('/follow', 'UserController@follow');
$router->post('/unfollow', 'UserController@unfollow');

// Dispatch the request
$router->dispatch();
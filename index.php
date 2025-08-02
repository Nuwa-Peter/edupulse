<?php
/**
 * EduPulse - Main Entry Point & Router
 *
 * This file handles all incoming web requests and routes them to the appropriate page handlers.
 * It uses the FastRoute library for efficient and clean routing.
 */

// --- 1. BOOTSTRAP ---
// Start session, load configuration, and initialize essential functions.
// The config.php file is expected to handle session_start(), error reporting, and load dependencies.
if (!file_exists(__DIR__ . '/includes/config.php')) {
    die('<h1>Critical Error</h1><p>Configuration file is missing. Please ensure <code>includes/config.php</code> exists.</p>');
}
require_once __DIR__ . '/includes/config.php';

// Composer's autoloader
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    die('<h1>Critical Error</h1><p>Composer dependencies not found. Please run <code>composer install</code> in the project root.</p>');
}
require_once __DIR__ . '/vendor/autoload.php';


// --- 2. ROUTE DEFINITION ---
// Define the application's routes. Each route maps a URL to a handler file in the /pages directory.
$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    // Base directory for page files
    $pagesDir = __DIR__ . '/pages/';

    // General & Authentication
    $r->addRoute('GET', '/', $pagesDir . 'login.php');
    $r->addRoute(['GET', 'POST'], '/login', $pagesDir . 'login.php');
    $r->addRoute(['GET', 'POST'], '/register-school', $pagesDir . 'register_school.php');
    $r->addRoute('GET', '/dashboard', $pagesDir . 'dashboard.php');
    $r->addRoute(['GET', 'POST'], '/forgot-password', $pagesDir . 'forgot_password.php');
    $r->addRoute(['GET', 'POST'], '/reset-password', $pagesDir . 'reset_password.php');
    $r->addRoute('GET', '/profile', $pagesDir . 'profile.php');
    $r->addRoute('GET', '/school-info', $pagesDir . 'school_info.php');

    // Academic Management
    $r->addRoute('GET', '/classes', $pagesDir . 'classes.php');
    $r->addRoute('GET', '/subjects', $pagesDir . 'subjects.php');
    $r->addRoute('GET', '/marks', $pagesDir . 'marks.php');
    $r->addRoute('GET', '/grading-scales', $pagesDir . 'grading_scales.php');
    $r->addRoute('GET', '/report-card', $pagesDir . 'report_card.php');
    $r->addRoute('GET', '/student-id', $pagesDir . 'student_id.php');
    $r->addRoute('GET', '/teacher-id', $pagesDir . 'teacher_id.php');
    $r->addRoute('GET', '/historical-performance', $pagesDir . 'historical_performance.php');
    $r->addRoute('GET', '/timetable', $pagesDir . 'timetable.php');
    $r->addRoute('GET', '/attendance', $pagesDir . 'attendance.php');
    $r->addRoute('GET', '/elearning', $pagesDir . 'elearning.php');

    // User Management
    $r->addRoute('GET', '/students', $pagesDir . 'students.php');
    $r->addRoute('GET', '/teachers', $pagesDir . 'teachers.php');
    $r->addRoute('GET', '/admissions', $pagesDir . 'admissions.php');

    // Financial Management
    $r->addRoute('GET', '/fees', $pagesDir . 'fees.php');

    // Communication
    $r->addRoute('GET', '/announcements', $pagesDir . 'announcements.php');
    $r->addRoute('GET', '/chat', $pagesDir . 'chat.php');
    $r->addRoute('POST', '/send-email', $pagesDir . 'send_email.php');
    $r->addRoute('POST', '/send-sms', $pagesDir . 'send_sms.php');

    // Parent & Student Portal
    $r->addRoute('GET', '/parent-portal', $pagesDir . 'parent_portal.php');

    // Superadmin & Headteacher Features
    $r->addRoute('GET', '/analytics', $pagesDir . 'analytics.php');
    $r->addRoute('GET', '/admin-access', $pagesDir . 'admin_access.php');
    $r->addRoute('GET', '/backups', $pagesDir . 'backups.php');
    $r->addRoute('GET', '/audit-logs', $pagesDir . 'audit_logs.php');
    $r->addRoute('GET', '/directory', $pagesDir . 'directory.php');
    $r->addRoute('GET', '/settings', $pagesDir . 'settings.php');
    $r->addRoute('GET', '/aoi', $pagesDir . 'aoi.php'); // Activities of Integration
    $r->addRoute('GET', '/surveys', $pagesDir . 'surveys.php');

    // Offline Mode
    $r->addRoute('GET', '/offline', $pagesDir . 'offline.php');
});


// --- 3. DISPATCH REQUEST ---
// Fetch the method and URI, then dispatch the request to the appropriate handler.
$httpMethod = $_SERVER['REQUEST_METHOD'];

// The URI needs to be stripped of the base path if EduPulse is not in the web root.
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = dirname($_SERVER['SCRIPT_NAME']);

// If the base path is part of the URI, remove it.
if ($basePath !== '/' && strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
}
if ($uri === false || $uri === '') {
    $uri = '/';
}

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);


// --- 4. AUTHENTICATION GUARDIAN ---
// Centralized logic to handle authentication checks and redirects.

// Public routes that do not require login.
$public_routes = [
    '/',
    '/login',
    '/register-school',
    '/forgot-password',
    '/reset-password',
    '/offline'
];

$is_logged_in = is_logged_in();
$is_public_route = in_array($uri, $public_routes);

// If user is logged in and tries to access a public page (like login), redirect to dashboard.
if ($is_logged_in && $is_public_route) {
    redirect('/dashboard');
}

// If user is not logged in and tries to access a private page, redirect to login.
if (!$is_logged_in && !$is_public_route) {
    // You can store the intended URL in the session to redirect back after login if desired
    // $_SESSION['intended_url'] = $uri;
    $_SESSION['error_message'] = "You must be logged in to access that page.";
    redirect('/login');
}


// --- 5. RENDER PAGE ---
// Based on the routing result, render the page or show an error.

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        require __DIR__ . '/includes/header.php';
        echo '<div class="container"><h1>404 - Page Not Found</h1><p>The page you are looking for does not exist.</p></div>';
        require __DIR__ . '/includes/footer.php';
        break;

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        require __DIR__ . '/includes/header.php';
        echo '<div class="container"><h1>405 - Method Not Allowed</h1><p>The requested method is not allowed for this route.</p></div>';
        require __DIR__ . '/includes/footer.php';
        break;

    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        if (file_exists($handler)) {
            extract($vars);
            require $handler;
        } else {
            http_response_code(500);
            require __DIR__ . '/includes/header.php';
            echo '<div class="container"><h1>500 - Internal Server Error</h1><p>The page handler file is missing.</p></div>';
            require __DIR__ . '/includes/footer.php';
        }
        break;
}
?>

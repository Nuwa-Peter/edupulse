<?php
/**
 * EduPulse - Main Entry Point & Router
 */

// Bootstrap
require_once __DIR__ . '/includes/config.php';

// This will be created later
// require_once __DIR__ . '/vendor/autoload.php';

// Basic routing logic
$route = $_GET['route'] ?? 'login';

$page_path = __DIR__ . '/pages/' . $route . '.php';

if (file_exists($page_path)) {
    require $page_path;
} else {
    http_response_code(404);
    echo "<h1>404 Not Found</h1>";
    echo "The page you requested was not found.";
}
?>

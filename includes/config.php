<?php
/**
 * EduPulse - Core Configuration File
 *
 * This file initializes essential settings, constants, and the database connection.
 * It is included at the beginning of all primary scripts.
 */

// --- 1. Error Reporting & Environment ---
// Set error reporting for development. In production, this should be logged, not displayed.
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set the default timezone for Uganda
date_default_timezone_set('Africa/Kampala');


// --- 2. Session Management ---
// Start a secure session.
if (session_status() == PHP_SESSION_NONE) {
    // Use secure session cookie settings in a production environment
    // session_set_cookie_params(['lifetime' => 86400, 'httponly' => true, 'samesite' => 'Lax']);
    session_start();
}


// --- 3. Application Constants ---
// Define root path and base URL for consistent asset linking and file includes.
define('APP_ROOT', dirname(__DIR__)); // The root directory of the project (edupulse/)
define('BASE_URL', 'http://localhost'); // The base URL of the application


// --- 4. Database Configuration ---
// Database credentials. Replace with your actual credentials.
define('DB_HOST', 'localhost');
define('DB_NAME', 'edupulsedb');
define('DB_USER', 'root'); // Default XAMPP username
define('DB_PASS', '');     // Default XAMPP password


// --- 5. Third-Party Service Keys (Pusher & Encryption) ---
define('PUSHER_APP_ID', '2031221');
define('PUSHER_APP_KEY', 'a4ca373308c83595ce40');
define('PUSHER_APP_SECRET', 'e5dc39b8c5e94f5c51b4');
define('PUSHER_CLUSTER', 'eu');

// For AES-256 chat encryption.
define('ENCRYPTION_KEY', 'EaPsgkL8J2gT9vYwZq4t7w!z%C*F-JaN');

// --- 6. Email Configuration (for PHPMailer) ---
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'nuwapeter2013@gmail.com');
define('SMTP_PASS', 'jxfi muyy mtwb wzjr');
define('SMTP_SECURE', 'tls');
define('EMAIL_FROM_ADDRESS', 'nuwapeter2013@gmail.com');
define('EMAIL_FROM_NAME', 'EduPulse');

// --- 7. Database Connection (PDO) ---
// Establish a persistent connection to the database.
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (\PDOException $e) {
    // In a real application, you would log this error and show a user-friendly message.
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}


// --- 7. Core Functions ---
// All helper functions are now in functions.php to prevent redeclaration errors.
require_once APP_ROOT . '/includes/functions.php';

?>

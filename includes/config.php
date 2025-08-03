<?php
/**
 * EduPulse - Core Configuration File
 *
 * This file initializes essential settings, constants, and the database connection.
 * It is included at the beginning of all primary scripts.
 */

// --- 1. Error Reporting & Environment ---
error_reporting(E_ALL);
ini_set('display_errors', 1); // Set to 0 in production
date_default_timezone_set('Africa/Kampala');

// --- 2. Session Management ---
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- 3. Application Constants ---
define('APP_ROOT', dirname(__DIR__));
define('BASE_URL', 'http://localhost/edupulse'); // Hardcoded for this specific deployment

// --- 4. Database Configuration ---
define('DB_HOST', 'localhost');
define('DB_NAME', 'edupulsedb');
define('DB_USER', 'root');
define('DB_PASS', '');

// --- 5. Third-Party Service Keys ---
define('PUSHER_APP_ID', 'YOUR_PUSHER_APP_ID');
define('PUSHER_APP_KEY', 'YOUR_PUSHER_APP_KEY');
define('PUSHER_APP_SECRET', 'YOUR_PUSHER_APP_SECRET');
define('PUSHER_CLUSTER', 'YOUR_PUSHER_CLUSTER');
define('ENCRYPTION_KEY', 'some-long-random-string-for-aes-256');

// --- 6. Email Configuration (PHPMailer) ---
define('SMTP_HOST', 'smtp.example.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@example.com');
define('SMTP_PASS', 'your-email-password');
define('SMTP_SECURE', 'tls');
define('EMAIL_FROM_ADDRESS', 'noreply@edupulse.com');
define('EMAIL_FROM_NAME', 'EduPulse');

// --- 7. Database Connection (PDO) ---
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
    die("Database connection failed: " . $e->getMessage());
}

// --- 8. License Check ---
function verify_school_license($pdo, $school_id) {
    if (!$school_id) return false;
    $stmt = $pdo->prepare("SELECT status, expiry_date FROM licenses WHERE school_id = :school_id");
    $stmt->execute(['school_id' => $school_id]);
    $license = $stmt->fetch();

    if (!$license || $license['status'] !== 'active' || strtotime($license['expiry_date']) < time()) {
        return false;
    }
    return true;
}

// --- 9. Core Functions ---
require_once APP_ROOT . '/includes/functions.php';

?>

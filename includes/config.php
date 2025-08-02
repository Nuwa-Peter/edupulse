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
define('BASE_URL', 'http://localhost/edupulse'); // The base URL of the application


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

// --- 6. Database Connection (PDO) ---
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

/**
 * Checks if a user is logged in.
 * @return bool True if logged in, false otherwise.
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Gets the current logged-in user's data from the session.
 * @return array|null The user data array or null if not logged in.
 */
function get_current_user() {
    return $_SESSION['user'] ?? null;
}

/**
 * Verifies the school's license status.
 * This is a critical function for the commercial viability of the application.
 *
 * @param PDO $pdo The database connection object.
 * @param int $school_id The ID of the school to check.
 * @return bool True if the license is valid and active, false otherwise.
 */
function verify_school_license(PDO $pdo, int $school_id): bool {
    // Superadmins do not belong to a school and bypass this check.
    if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'Superadmin') {
        return true;
    }

    try {
        $stmt = $pdo->prepare(
            "SELECT status, expiry_date FROM licenses WHERE school_id = :school_id"
        );
        $stmt->execute(['school_id' => $school_id]);
        $license = $stmt->fetch();

        if (!$license) {
            // No license found for this school.
            return false;
        }

        // Check if the license is active and not expired.
        $is_active = $license['status'] === 'active';
        $is_not_expired = strtotime($license['expiry_date']) >= time();

        return $is_active && $is_not_expired;

    } catch (PDOException $e) {
        // Log the error in a real application
        // error_log('License check failed: ' . $e->getMessage());
        return false;
    }
}

// --- 8. Include other helper functions ---
// In a larger application, you would include other function files here.
// require_once APP_ROOT . '/includes/functions.php';

?>

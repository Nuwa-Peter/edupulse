<?php
/**
 * EduPulse - General Helper Functions
 *
 * This file contains various helper functions used throughout the application
 * for tasks like redirection, input sanitization, security checks, and formatting.
 */

// config.php should be included before this file.
if (!defined('BASE_URL')) {
    die('Error: Core configuration is not loaded.');
}

/**
 * Redirects the user to a specified URL.
 *
 * @param string $url The URL to redirect to, relative to the BASE_URL.
 */
if (!function_exists('redirect')) {
    function redirect(string $url): void {
        header("Location: " . BASE_URL . $url);
        exit();
    }
}

/**
 * Sanitizes a string to prevent XSS attacks.
 *
 * @param string $data The input string to sanitize.
 * @return string The sanitized string.
 */
if (!function_exists('sanitize')) {
    function sanitize(string $data): string {
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Formats a number as currency.
 *
 * @param float $amount The amount to format.
 * @param string $currency The currency symbol (default: 'UGX').
 * @return string The formatted currency string.
 */
if (!function_exists('format_currency')) {
    function format_currency(float $amount, string $currency = 'UGX'): string {
        return $currency . ' ' . number_format($amount, 2);
    }
}

/**
 * Checks if the current user's role is in the list of allowed roles.
 * If not, it can redirect them to a 'denied' page or the dashboard.
 *
 * @param array $allowed_roles An array of roles that are allowed to access the page.
 */
if (!function_exists('check_permission')) {
    function check_permission(array $allowed_roles): void {
        if (!is_logged_in()) {
            redirect('/login');
        }

        $user = get_current_user();

        // Superadmin bypass: A Superadmin can access any page.
        if (is_array($user) && isset($user['role']) && trim($user['role']) === 'Superadmin') {
            return;
        }

        // Defensive check for all other users.
        if (!is_array($user) || !isset($user['role']) || !in_array(trim($user['role']), $allowed_roles)) {
            $_SESSION['error_message'] = "You do not have permission to access that page, or your session has expired.";
            redirect('/login');
        }
    }
}

/**
 * Generates and stores a CSRF token in the session.
 *
 * @return string The generated token.
 */
if (!function_exists('generate_csrf_token')) {
    function generate_csrf_token(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

/**
 * Verifies a submitted CSRF token against the one in the session.
 *
 * @param string $token The token submitted from a form.
 * @return bool True if the token is valid, false otherwise.
 */
if (!function_exists('verify_csrf_token')) {
    function verify_csrf_token(string $token): bool {
        if (isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token)) {
            // Token is valid, unset it to prevent reuse
            unset($_SESSION['csrf_token']);
            return true;
        }
        return false;
    }
}

/**
 * Displays a session-based flash message (e.g., for success or error alerts).
 * The message is cleared after being displayed once.
 *
 * @param string $key The session key for the message (e.g., 'success_message').
 * @param string $alert_class The Bootstrap alert class (e.g., 'alert-success').
 */
if (!function_exists('display_flash_message')) {
    function display_flash_message(string $key, string $alert_class = 'alert-danger'): void {
        if (isset($_SESSION[$key])) {
            echo '<div class="alert ' . $alert_class . ' alert-dismissible fade show" role="alert">';
            echo sanitize($_SESSION[$key]);
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
            unset($_SESSION[$key]);
        }
    }
}

/**
 * Encrypts a string using AES-256-CBC.
 * @param string $plaintext The text to encrypt.
 * @return string|false The encrypted string or false on failure.
 */
if (!function_exists('encrypt_data')) {
    function encrypt_data(string $plaintext) {
        $ivlen = openssl_cipher_iv_length($cipher = "AES-256-CBC");
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($plaintext, $cipher, ENCRYPTION_KEY, $options=OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, ENCRYPTION_KEY, $as_binary=true);
        return base64_encode($iv . $hmac . $ciphertext_raw);
    }
}

/**
 * Decrypts a string using AES-256-CBC.
 * @param string $ciphertext The encrypted text.
 * @return string|false The decrypted string or false on failure.
 */
if (!function_exists('decrypt_data')) {
    function decrypt_data(string $ciphertext) {
        $c = base64_decode($ciphertext);
        $ivlen = openssl_cipher_iv_length($cipher = "AES-256-CBC");
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len=32);
        $ciphertext_raw = substr($c, $ivlen + $sha2len);
        $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, ENCRYPTION_KEY, $options=OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, ENCRYPTION_KEY, $as_binary=true);
        if (hash_equals($hmac, $calcmac)) {
            return $original_plaintext;
        }
        return false;
    }
}

// --- Functions moved from config.php ---

/**
 * Checks if a user is logged in.
 * @return bool True if logged in, false otherwise.
 */
if (!function_exists('is_logged_in')) {
    function is_logged_in() {
        return isset($_SESSION['user_id']);
    }
}

/**
 * Gets the current logged-in user's data from the session.
 * @return array|null The user data array or null if not logged in.
 */
if (!function_exists('get_current_user')) {
    function get_current_user() {
        return $_SESSION['user'] ?? null;
    }
}

/**
 * Verifies the school's license status.
 * This is a critical function for the commercial viability of the application.
 *
 * @param PDO $pdo The database connection object.
 * @param int $school_id The ID of the school to check.
 * @return bool True if the license is valid and active, false otherwise.
 */
if (!function_exists('verify_school_license')) {
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
}
?>

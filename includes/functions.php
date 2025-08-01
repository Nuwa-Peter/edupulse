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
function redirect(string $url): void {
    header("Location: " . BASE_URL . $url);
    exit();
}

/**
 * Sanitizes a string to prevent XSS attacks.
 *
 * @param string $data The input string to sanitize.
 * @return string The sanitized string.
 */
function sanitize(string $data): string {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Formats a number as currency.
 *
 * @param float $amount The amount to format.
 * @param string $currency The currency symbol (default: 'UGX').
 * @return string The formatted currency string.
 */
function format_currency(float $amount, string $currency = 'UGX'): string {
    return $currency . ' ' . number_format($amount, 2);
}

/**
 * Checks if the current user's role is in the list of allowed roles.
 * If not, it can redirect them to a 'denied' page or the dashboard.
 *
 * @param array $allowed_roles An array of roles that are allowed to access the page.
 */
function check_permission(array $allowed_roles): void {
    if (!is_logged_in()) {
        redirect('/login');
    }

    $user = get_current_user();
    if (!$user || !in_array($user['role'], $allowed_roles)) {
        // You could redirect to a dedicated 'access-denied' page
        $_SESSION['error_message'] = "You do not have permission to access this page.";
        redirect('/dashboard');
    }
}

/**
 * Generates and stores a CSRF token in the session.
 *
 * @return string The generated token.
 */
function generate_csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifies a submitted CSRF token against the one in the session.
 *
 * @param string $token The token submitted from a form.
 * @return bool True if the token is valid, false otherwise.
 */
function verify_csrf_token(string $token): bool {
    if (isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token)) {
        // Token is valid, unset it to prevent reuse
        unset($_SESSION['csrf_token']);
        return true;
    }
    return false;
}

/**
 * Displays a session-based flash message (e.g., for success or error alerts).
 * The message is cleared after being displayed once.
 *
 * @param string $key The session key for the message (e.g., 'success_message').
 * @param string $alert_class The Bootstrap alert class (e.g., 'alert-success').
 */
function display_flash_message(string $key, string $alert_class = 'alert-danger'): void {
    if (isset($_SESSION[$key])) {
        echo '<div class="alert ' . $alert_class . ' alert-dismissible fade show" role="alert">';
        echo sanitize($_SESSION[$key]);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
        unset($_SESSION[$key]);
    }
}

/**
 * Encrypts a string using AES-256-CBC.
 * @param string $plaintext The text to encrypt.
 * @return string|false The encrypted string or false on failure.
 */
function encrypt_data(string $plaintext) {
    $ivlen = openssl_cipher_iv_length($cipher = "AES-256-CBC");
    $iv = openssl_random_pseudo_bytes($ivlen);
    $ciphertext_raw = openssl_encrypt($plaintext, $cipher, ENCRYPTION_KEY, $options=OPENSSL_RAW_DATA, $iv);
    $hmac = hash_hmac('sha256', $ciphertext_raw, ENCRYPTION_KEY, $as_binary=true);
    return base64_encode($iv . $hmac . $ciphertext_raw);
}

/**
 * Decrypts a string using AES-256-CBC.
 * @param string $ciphertext The encrypted text.
 * @return string|false The decrypted string or false on failure.
 */
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
?>

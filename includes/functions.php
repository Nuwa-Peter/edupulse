<?php
/**
 * EduPulse - General Helper Functions
 */

// config.php is already included by the time this file is, so BASE_URL is available.

/**
 * Redirects the user to a specified URL.
 * Appends the BASE_URL.
 * @param string $url The path to redirect to (e.g., '/login').
 */
function redirect(string $url): void {
    header("Location: " . BASE_URL . $url);
    exit();
}

/**
 * Checks if a user is currently logged in.
 * @return bool True if a user_id exists in the session, false otherwise.
 */
function is_logged_in(): bool {
    return isset($_SESSION['user_id']);
}

/**
 * Gets the current logged-in user's data from the session.
 * @return array|null The user data array or null if not logged in.
 */
function get_current_user(): ?array {
    return $_SESSION['user'] ?? null;
}

/**
 * Sanitizes a string to prevent XSS attacks.
 * @param string $data The input string.
 * @return string The sanitized string.
 */
function sanitize(string $data): string {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Generates and stores a CSRF token in the session.
 * @return string The generated CSRF token.
 */
function generate_csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifies a submitted CSRF token.
 * @param string $token The token from the form submission.
 * @return bool True if the token is valid, false otherwise.
 */
function verify_csrf_token(string $token): bool {
    if (isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token)) {
        // Invalidate the token after use to prevent reuse
        unset($_SESSION['csrf_token']);
        return true;
    }
    return false;
}

/**
 * Displays a session-based flash message.
 * @param string $key The key of the message in the session.
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
 * Formats a number as Ugandan Shillings (UGX).
 * @param float $amount The amount.
 * @return string The formatted currency string.
 */
function format_currency(float $amount): string {
    return 'UGX ' . number_format($amount, 0);
}

?>

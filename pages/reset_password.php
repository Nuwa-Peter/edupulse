<?php
/**
 * EduPulse - Reset Password Page
 *
 * Allows a user to set a new password using a valid token from email.
 */

$page_title = "Reset Password - EduPulse";
$body_class = "login-page";

$token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_STRING);
$error_message = '';
$success_message = '';
$is_token_valid = false;

if (empty($token)) {
    $error_message = "No reset token provided. Please use the link from your email.";
} else {
    try {
        // Check if token is valid and not expired
        $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = :token AND expires_at > NOW()");
        $stmt->execute(['token' => $token]);
        $reset_request = $stmt->fetch();

        if ($reset_request) {
            $is_token_valid = true;
            $email = $reset_request['email'];
        } else {
            $error_message = "This password reset link is invalid or has expired. Please request a new one.";
        }
    } catch (PDOException $e) {
        $error_message = "A database error occurred.";
        // error_log("Reset Password Token Check PDOException: " . $e->getMessage());
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_token_valid) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error_message = "Invalid request. Please try again.";
    } else {
        $password = $_POST['password'];
        $password_confirm = $_POST['password_confirm'];
        $submitted_token = $_POST['token'];

        // Extra check to ensure token matches
        if ($submitted_token !== $token) {
            $error_message = "Token mismatch. Please try again.";
        } elseif (empty($password) || empty($password_confirm)) {
            $error_message = "Please enter and confirm your new password.";
        } elseif ($password !== $password_confirm) {
            $error_message = "Passwords do not match.";
        } elseif (strlen($password) < 8) {
            $error_message = "Password must be at least 8 characters long.";
        } else {
            try {
                // Hash the new password
                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                // Update user's password
                $update_stmt = $pdo->prepare("UPDATE users SET password_hash = :password_hash WHERE email = :email");
                $update_stmt->execute(['password_hash' => $password_hash, 'email' => $email]);

                // Invalidate the token by deleting it
                $delete_stmt = $pdo->prepare("DELETE FROM password_resets WHERE email = :email");
                $delete_stmt->execute(['email' => $email]);

                $success_message = "Your password has been reset successfully! You can now log in with your new password.";
                $is_token_valid = false; // Hide form after successful reset

            } catch (PDOException $e) {
                $error_message = "An error occurred while updating your password.";
                // error_log("Reset Password Update PDOException: " . $e->getMessage());
            }
        }
    }
}

require_once APP_ROOT . '/includes/header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="login-container mt-5">
                <h2 class="text-center mb-4">Set a New Password</h2>

                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger"><?= sanitize($error_message) ?></div>
                <?php endif; ?>

                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success"><?= sanitize($success_message) ?></div>
                    <div class="text-center">
                        <a href="<?= BASE_URL ?>/login" class="btn btn-primary">Go to Login</a>
                    </div>
                <?php endif; ?>

                <?php if ($is_token_valid): ?>
                <form action="<?= BASE_URL ?>/reset-password?token=<?= sanitize($token) ?>" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                    <input type="hidden" name="token" value="<?= sanitize($token) ?>">
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirm" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Reset Password</button>
                    </div>
                </form>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<?php
// Minimal footer for auth pages
?>
    </div> <!-- Close #content -->
</div> <!-- Close .wrapper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

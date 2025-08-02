<?php
/**
 * EduPulse - Forgot Password Page
 *
 * Allows users to request a password reset link via email.
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../vendor/autoload.php'; // Adjust path if needed, but router should handle it.

$page_title = "Forgot Password - EduPulse";
$body_class = "login-page"; // Reuse login page styling

$message = '';
$message_type = 'info'; // 'info' or 'success'

// Default message
$message = "Please enter your email address. If an account exists, we will send a password reset link.";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $message = "Invalid request. Please try again.";
    } else {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

        if (!empty($email)) {
            try {
                $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = :email AND status = 'active'");
                $stmt->execute(['email' => $email]);
                $user = $stmt->fetch();

                // If user exists, generate token and send email
                if ($user) {
                    // Generate a secure token
                    $token = bin2hex(random_bytes(50));
                    $expires = new DateTime('+1 hour');
                    $expires_at = $expires->format('Y-m-d H:i:s');

                    // NOTE: A `password_resets` table is required for this to work.
                    // CREATE TABLE `password_resets` (`id` INT AUTO_INCREMENT PRIMARY KEY, `email` VARCHAR(100), `token` VARCHAR(100), `expires_at` DATETIME);
                    $reset_stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires_at)");
                    $reset_stmt->execute(['email' => $email, 'token' => $token, 'expires_at' => $expires_at]);

                    // Send the email using PHPMailer
                    $reset_link = BASE_URL . '/reset-password?token=' . $token;

                    // Send the email using PHPMailer
                    $reset_link = BASE_URL . '/reset-password?token=' . $token;

                    $mail = new PHPMailer(true);
                    try {
                        // Server settings from config.php
                        $mail->isSMTP();
                        $mail->Host       = SMTP_HOST;
                        $mail->SMTPAuth   = true;
                        $mail->Username   = SMTP_USER;
                        $mail->Password   = SMTP_PASS;
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                        $mail->Port       = SMTP_PORT;

                        //Recipients
                        $mail->setFrom(EMAIL_FROM_ADDRESS, EMAIL_FROM_NAME);
                        $mail->addAddress($email);

                        // Content
                        $mail->isHTML(true);
                        $mail->Subject = 'Password Reset Request for EduPulse';
                        $mail->Body    = "Hello,<br><br>You requested a password reset. Please click the link below to reset your password. This link is valid for 1 hour.<br><br><a href='{$reset_link}'>{$reset_link}</a><br><br>If you did not request this, please ignore this email.";
                        $mail->AltBody = "Hello,\n\nYou requested a password reset. Please use the following link, which is valid for 1 hour: \n" . $reset_link . "\n\nIf you did not request this, please ignore this email.";

                        $mail->send();
                    } catch (Exception $e) {
                        // Don't expose detailed error to user, but log it
                        error_log("Mailer Error: " . $mail->ErrorInfo);
                    }
                }
            } catch (PDOException $e) {
                // Log error, but don't expose it to the user
                // error_log("Forgot Password PDOException: " . $e->getMessage());
            }
        }

        // Always show a generic success message to prevent email enumeration
        $message = "If an account with that email address exists, a password reset link has been sent. Please check your inbox.";
        $message_type = 'success';
    }
}

require_once APP_ROOT . '/includes/header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="login-container mt-5">
                <h2 class="text-center mb-4">Forgot Your Password?</h2>

                <div class="alert alert-<?= $message_type === 'success' ? 'success' : 'info' ?>">
                    <?= sanitize($message) ?>
                </div>

                <?php if ($message_type !== 'success'): ?>
                <form action="<?= BASE_URL ?>/forgot-password" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                    <div class="mb-3">
                        <label for="email" class="form-label">Enter your account's email address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Send Reset Link</button>
                    </div>
                </form>
                <?php endif; ?>

                <div class="text-center mt-3">
                    <a href="<?= BASE_URL ?>/login" class="text-decoration-none"><i class="fas fa-arrow-left me-2"></i>Back to Login</a>
                </div>
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

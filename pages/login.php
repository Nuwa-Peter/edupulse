<?php
/**
 * EduPulse - Login Page
 *
 * This page handles user authentication for all roles that use email/password.
 * It also serves as the main landing page for the application.
 */

// config.php is already included by index.php, which routes to this page.
// functions.php is also expected to be available.

$page_title = "Login - EduPulse";
$body_class = "login-page"; // Custom class for the body tag

$error_message = '';

// The authentication check is now handled by the central guardian in index.php.
// This prevents redirect loops.

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // For now, we only handle email/password login. OTP would be a separate form/logic.
    if (isset($_POST['login'])) {
        // CSRF Check
        if (!verify_csrf_token($_POST['csrf_token'])) {
            $error_message = "Invalid request. Please try again.";
        } else {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];

            if (empty($email) || empty($password)) {
                $error_message = "Email and password are required.";
            } else {
                try {
                    // Fetch user from database
                    $stmt = $pdo->prepare("SELECT u.*, s.name as school_name FROM users u LEFT JOIN schools s ON u.school_id = s.school_id WHERE u.email = :email");
                    $stmt->execute(['email' => $email]);
                    $user = $stmt->fetch();

                    // Verify user and password
                    if ($user && password_verify($password, $user['password_hash'])) {

                        // Check if account is active
                        if ($user['status'] !== 'active') {
                            $error_message = "Your account is inactive or suspended. Please contact support.";
                        } else {
                            // Check school license (if not a Superadmin)
                            $license_valid = ($user['role'] === 'Superadmin') || verify_school_license($pdo, $user['school_id']);

                            if (!$license_valid) {
                                $error_message = "Your school's license has expired or is invalid. Please contact your Headteacher.";
                            } else {
                                // --- Login Successful ---
                                // Regenerate session ID to prevent session fixation
                                session_regenerate_id(true);

                                // Store user data in session
                                $_SESSION['user_id'] = $user['user_id'];
                                $_SESSION['edupulse_id'] = $user['edupulse_id'];
                                $_SESSION['user'] = [
                                    'role' => $user['role'],
                                    'first_name' => $user['first_name'],
                                    'school_id' => $user['school_id'],
                                    'school_name' => $user['school_name'],
                                    'profile_photo_url' => $user['profile_photo_url']
                                ];

                                // Update last login timestamp
                                $update_stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE user_id = :user_id");
                                $update_stmt->execute(['user_id' => $user['user_id']]);

                                // Redirect to dashboard
                                redirect('/dashboard');
                            }
                        }
                    } else {
                        $error_message = "Invalid email or password.";
                    }
                } catch (PDOException $e) {
                    // In production, log this error instead of showing it
                    $error_message = "A database error occurred. Please try again later.";
                    // error_log("Login PDOException: " . $e->getMessage());
                }
            }
        }
    }
}

// We need to override the body class defined in the header
function set_body_class() {
    return 'login-page';
}

// Include header
require_once APP_ROOT . '/includes/header.php';
?>

<div class="container">
    <div class="row align-items-center" style="min-height: 80vh;">
        <!-- Hero Section -->
        <div class="col-md-7 text-white login-hero">
            <h1 class="display-3 fw-bold">Welcome to EduPulse</h1>
            <p class="lead">The future of school management in Uganda. Streamline operations, empower teachers, and engage parents like never before.</p>
            <hr class="my-4">
            <ul class="list-unstyled">
                <li class="mb-2"><i class="fas fa-check-circle me-2"></i> AI-Powered Performance Insights</li>
                <li class="mb-2"><i class="fas fa-check-circle me-2"></i> Real-time Parent-Teacher Communication</li>
                <li class="mb-2"><i class="fas fa-check-circle me-2"></i> QR Code-Based Attendance Tracking</li>
            </ul>
        </div>

        <!-- Login Form -->
        <div class="col-md-5">
            <div class="login-container">
                <h2 class="text-center mb-4">Admin & Staff Login</h2>

                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger"><?= $error_message ?></div>
                <?php endif; ?>

                <form action="<?= BASE_URL ?>/login" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" name="login" class="btn btn-primary">Log In</button>
                    </div>
                    <div class="text-center mt-3">
                        <a href="<?= BASE_URL ?>/forgot-password" class="text-decoration-none">Forgot Password?</a>
                    </div>
                </form>
                <hr>
                <div class="text-center">
                    <p class="mb-1">Students & Parents:</p>
                    <button class="btn btn-outline-secondary btn-sm" disabled>Login with EduPulse ID & OTP (Coming Soon)</button>
                </div>
                 <div class="text-center mt-4">
                    <a href="<?= BASE_URL ?>/register-school" class="btn btn-success">Register a New School</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// We don't need the standard footer with attribution here, so we create a minimal one.
?>
    </div> <!-- Close #content -->
</div> <!-- Close .wrapper -->
<!-- JS Includes -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/custom.js"></script>
</body>
</html>

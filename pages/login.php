<?php
/**
 * EduPulse - Login Page
 */

// config.php is included by index.php, which routes to this page.
// functions.php is also available.

$page_title = "Login";
$body_class = "login-page"; // Custom class for the body

// Redirect if already logged in
if (is_logged_in()) {
    redirect('/index.php?route=dashboard');
}

$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    // For now, we only handle email/password login for non-student/parent roles
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

            // Verify user, password, and that the role is one that uses password login
            $password_roles = ['Superadmin', 'Headteacher', 'Deputy Headteacher', 'DOS', 'Bursar'];
            if ($user && in_array($user['role'], $password_roles) && password_verify($password, $user['password_hash'])) {

                // Check if account is active
                if ($user['status'] !== 'active') {
                    $error_message = "Your account is inactive or suspended. Please contact support.";
                }
                // Check school license (if not a Superadmin)
                else if ($user['role'] !== 'Superadmin' && !verify_school_license($pdo, $user['school_id'])) {
                     $error_message = "Your school's license has expired or is invalid. Please contact your Headteacher.";
                }
                else {
                    // --- Login Successful ---
                    session_regenerate_id(true);

                    // Store user data in session
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['user'] = [
                        'user_id' => $user['user_id'],
                        'edupulse_id' => $user['edupulse_id'],
                        'role' => $user['role'],
                        'first_name' => $user['first_name'],
                        'last_name' => $user['last_name'],
                        'school_id' => $user['school_id'],
                        'school_name' => $user['school_name'],
                        'profile_photo_url' => $user['profile_photo_url']
                    ];

                    // Update last login timestamp
                    $update_stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE user_id = :user_id");
                    $update_stmt->execute(['user_id' => $user['user_id']]);

                    // Redirect to dashboard
                    redirect('/index.php?route=dashboard');
                }
            } else {
                $error_message = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            $error_message = "A database error occurred. Please try again later.";
            // In production, log this error: error_log("Login PDOException: " . $e->getMessage());
        }
    }
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

                <form action="<?= BASE_URL ?>/index.php?route=login" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" name="login" class="btn btn-primary w-100">Log In</button>
                    </div>
                    <div class="text-center mt-3">
                        <a href="<?= BASE_URL ?>/index.php?route=forgot_password" class="text-decoration-none">Forgot Password?</a>
                    </div>
                </form>
                <hr>
                <div class="text-center">
                    <p class="mb-1">Students & Parents:</p>
                    <button class="btn btn-outline-secondary btn-sm" disabled>Login with EduPulse ID & OTP (Coming Soon)</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// We don't need the standard footer for the login page to have the full-screen effect
// but we need to close the tags from header.php
?>
    </div> <!-- Close #content -->
    </div> <!-- Close #page-content-wrapper -->
</div> <!-- Close #wrapper -->

<!-- JS Includes -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/custom.js"></script>
</body>
</html>

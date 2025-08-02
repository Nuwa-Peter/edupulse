<?php
/**
 * EduPulse - School Registration Page
 *
 * Allows new schools to register for the service. Creates a new school
 * and a Headteacher administrator account.
 */

$page_title = "Register Your School - EduPulse";
$body_class = "login-page"; // Reuse login page styling

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error_message = "Invalid request. Please try again.";
    } else {
        // --- Sanitize and retrieve form data ---
        // School Info
        $school_name = sanitize($_POST['school_name']);
        $school_level = sanitize($_POST['school_level']);
        $school_address = sanitize($_POST['school_address']);
        $school_email = filter_input(INPUT_POST, 'school_email', FILTER_SANITIZE_EMAIL);
        $school_phone = sanitize($_POST['school_phone']);
        $latitude = filter_input(INPUT_POST, 'latitude', FILTER_VALIDATE_FLOAT);
        $longitude = filter_input(INPUT_POST, 'longitude', FILTER_VALIDATE_FLOAT);

        // Headteacher Info
        $ht_first_name = sanitize($_POST['ht_first_name']);
        $ht_last_name = sanitize($_POST['ht_last_name']);
        $ht_email = filter_input(INPUT_POST, 'ht_email', FILTER_SANITIZE_EMAIL);
        $ht_password = $_POST['ht_password'];
        $ht_password_confirm = $_POST['ht_password_confirm'];

        // --- Validation ---
        if ($ht_password !== $ht_password_confirm) {
            $error_message = "Headteacher passwords do not match.";
        } elseif (strlen($ht_password) < 8) {
            $error_message = "Password must be at least 8 characters long.";
        } else {
            try {
                // Check if school or headteacher email already exists
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM schools WHERE name = ?");
                $stmt->execute([$school_name]);
                if ($stmt->fetchColumn() > 0) {
                    $error_message = "A school with this name is already registered.";
                }

                $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
                $stmt->execute([$ht_email]);
                if ($stmt->fetchColumn() > 0) {
                    $error_message = "This email address is already in use.";
                }

                if (empty($error_message)) {
                    // --- Use a transaction for atomic inserts ---
                    $pdo->beginTransaction();

                    // 1. Create School
                    $edupulse_school_id = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $school_name), 0, 4)) . rand(1000, 9999);
                    $school_stmt = $pdo->prepare(
                        "INSERT INTO schools (edupulse_id, name, level, address, contact_email, contact_phone, latitude, longitude)
                         VALUES (:edupulse_id, :name, :level, :address, :email, :phone, :lat, :lon)"
                    );
                    $school_stmt->execute([
                        'edupulse_id' => $edupulse_school_id,
                        'name' => $school_name,
                        'level' => $school_level,
                        'address' => $school_address,
                        'email' => $school_email,
                        'phone' => $school_phone,
                        'lat' => $latitude,
                        'lon' => $longitude
                    ]);
                    $school_id = $pdo->lastInsertId();

                    // 2. Create Headteacher User
                    $edupulse_ht_id = 'HT-' . $edupulse_school_id;
                    $password_hash = password_hash($ht_password, PASSWORD_DEFAULT);
                    $user_stmt = $pdo->prepare(
                        "INSERT INTO users (edupulse_id, school_id, first_name, last_name, email, password_hash, role, status)
                         VALUES (:edupulse_id, :school_id, :first_name, :last_name, :email, :password_hash, 'Headteacher', 'active')"
                    );
                    $user_stmt->execute([
                        'edupulse_id' => $edupulse_ht_id,
                        'school_id' => $school_id,
                        'first_name' => $ht_first_name,
                        'last_name' => $ht_last_name,
                        'email' => $ht_email,
                        'password_hash' => $password_hash
                    ]);

                    // 3. Create a trial license
                    $license_key = $edupulse_school_id . '-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(4)));
                    $expiry_date = new DateTime('+30 days');
                    $license_stmt = $pdo->prepare(
                        "INSERT INTO licenses (school_id, license_key, expiry_date, status) VALUES (:school_id, :key, :expiry, 'active')"
                    );
                    $license_stmt->execute([
                        'school_id' => $school_id,
                        'key' => $license_key,
                        'expiry' => $expiry_date->format('Y-m-d')
                    ]);

                    $pdo->commit();
                    $success_message = "Registration successful! Your school and Headteacher account have been created. You can now log in.";

                }
            } catch (PDOException $e) {
                $pdo->rollBack();
                $error_message = "A database error occurred during registration. Please try again.";
                // error_log("Registration PDOException: " . $e->getMessage());
            }
        }
    }
}

require_once APP_ROOT . '/includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card">
                <div class="card-header text-center fs-4">
                    Register Your School with EduPulse
                </div>
                <div class="card-body p-4">
                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger"><?= $error_message ?></div>
                    <?php endif; ?>
                    <?php if (!empty($success_message)): ?>
                        <div class="alert alert-success"><?= $success_message ?></div>
                        <div class="text-center">
                            <a href="<?= BASE_URL ?>/login" class="btn btn-primary">Proceed to Login</a>
                        </div>
                    <?php else: ?>
                        <form action="<?= BASE_URL ?>/register-school" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">

                            <h5 class="text-primary">School Information</h5>
                            <hr>
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="school_name" class="form-label">School Name</label>
                                    <input type="text" class="form-control" id="school_name" name="school_name" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="school_level" class="form-label">School Level</label>
                                    <select class="form-select" id="school_level" name="school_level" required>
                                        <option value="Primary">Primary</option>
                                        <option value="Secondary">Secondary</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="school_address" class="form-label">Address</label>
                                <textarea class="form-control" id="school_address" name="school_address" rows="2" required></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="school_email" class="form-label">School Contact Email</label>
                                    <input type="email" class="form-control" id="school_email" name="school_email" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="school_phone" class="form-label">School Contact Phone</label>
                                    <input type="tel" class="form-control" id="school_phone" name="school_phone" required>
                                </div>
                            </div>
                            <div class="row align-items-end">
                                <div class="col-md-4 mb-3">
                                    <label for="latitude" class="form-label">Latitude</label>
                                    <input type="text" class="form-control" id="latitude" name="latitude">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="longitude" class="form-label">Longitude</label>
                                    <input type="text" class="form-control" id="longitude" name="longitude">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <button type="button" id="getLocationBtn" class="btn btn-secondary w-100">Get Location</button>
                                    <div id="locationStatus" class="form-text"></div>
                                </div>
                            </div>

                            <h5 class="text-primary mt-4">Headteacher Information</h5>
                            <hr>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="ht_first_name" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="ht_first_name" name="ht_first_name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="ht_last_name" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="ht_last_name" name="ht_last_name" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="ht_email" class="form-label">Email (This will be your login)</label>
                                <input type="email" class="form-control" id="ht_email" name="ht_email" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="ht_password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="ht_password" name="ht_password" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="ht_password_confirm" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="ht_password_confirm" name="ht_password_confirm" required>
                                </div>
                            </div>

                            <h5 class="text-primary mt-4">School Logo</h5>
                            <hr>
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="school_logo" class="form-label">Upload School Logo (Max 2MB)</label>
                                    <input type="file" class="form-control" id="school_logo" name="school_logo" accept="image/png, image/jpeg">
                                </div>
                                <div class="col-md-4 mb-3 text-center">
                                    <img id="logoPreview" src="https://via.placeholder.com/150?text=Logo+Preview" alt="Logo Preview" class="img-thumbnail" style="max-height: 100px;">
                                </div>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">Complete Registration</button>
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
</div>

<?php require_once APP_ROOT . '/includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const schoolLogoInput = document.getElementById('school_logo');
    const logoPreview = document.getElementById('logoPreview');

    schoolLogoInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                logoPreview.src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });
});
</script>

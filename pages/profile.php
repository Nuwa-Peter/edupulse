<?php
/**
 * EduPulse - User Profile Page
 *
 * Displays the logged-in user's profile information and allows updates.
 */

$page_title = "My Profile";
check_permission(['Superadmin', 'Headteacher', 'Deputy Headteacher', 'DOS', 'Bursar', 'Teacher', 'Student', 'Parent']);
$currentUser = get_current_user();
$user_id = $_SESSION['user_id'];

$error_message = '';
$success_message = '';

// Handle Profile Information Update
if (isset($_POST['update_profile'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error_message = "Invalid request.";
    } else {
        $first_name = sanitize($_POST['first_name']);
        $last_name = sanitize($_POST['last_name']);
        $phone = sanitize($_POST['phone']);

        try {
            $stmt = $pdo->prepare("UPDATE users SET first_name = :first_name, last_name = :last_name, phone = :phone WHERE user_id = :user_id");
            $stmt->execute(['first_name' => $first_name, 'last_name' => $last_name, 'phone' => $phone, 'user_id' => $user_id]);
            $success_message = "Profile updated successfully.";
            // Refresh user data in session
            $_SESSION['user']['first_name'] = $first_name;
        } catch (PDOException $e) {
            $error_message = "Failed to update profile.";
        }
    }
}

// Handle Password Change
if (isset($_POST['change_password'])) {
     if (!verify_csrf_token($_POST['csrf_token'])) {
        $error_message = "Invalid request.";
    } else {
        // ... (Password change logic would go here, including current password check)
        $success_message = "Password change functionality to be implemented.";
    }
}

// Handle Profile Photo Upload
if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
    // ... (Secure file upload logic would go here)
    $success_message = "Photo upload functionality to be implemented.";
}


// Fetch fresh user data for display
try {
    $stmt = $pdo->prepare("SELECT u.*, s.name as school_name FROM users u LEFT JOIN schools s ON u.school_id = s.school_id WHERE u.user_id = :id");
    $stmt->execute(['id' => $user_id]);
    $user_data = $stmt->fetch();
} catch (PDOException $e) {
    die("Could not fetch user profile.");
}

require_once APP_ROOT . '/includes/header.php';
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">My Profile</h1>

    <?php if ($error_message) echo "<div class='alert alert-danger'>$error_message</div>"; ?>
    <?php if ($success_message) echo "<div class='alert alert-success'>$success_message</div>"; ?>

    <div class="row">
        <!-- Profile Card -->
        <div class="col-lg-4">
            <div class="card shadow mb-4 text-center">
                <div class="card-body">
                    <img src="<?= BASE_URL . '/' . sanitize($user_data['profile_photo_url']) ?>" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;" alt="Profile Photo">
                    <h4 class="card-title"><?= sanitize($user_data['first_name'] . ' ' . $user_data['last_name']) ?></h4>
                    <p class="card-text text-muted"><?= sanitize($user_data['role']) ?></p>
                    <p class="card-text"><?= sanitize($user_data['school_name'] ?? 'EduPulse System') ?></p>
                    <hr>
                    <div class="d-flex justify-content-center" id="profileQrCode" data-value="<?= BASE_URL . '/profile?id=' . sanitize($user_data['edupulse_id']) ?>"></div>
                    <p class="small text-muted mt-2">Your EduPulse ID: <?= sanitize($user_data['edupulse_id']) ?></p>
                </div>
            </div>
        </div>

        <!-- Profile Forms -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Edit Profile Information</h6>
                </div>
                <div class="card-body">
                    <form id="profileInfoForm">
                        <div id="profile-info-messages" class="mb-3"></div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-control" value="<?= sanitize($user_data['first_name']) ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control" value="<?= sanitize($user_data['last_name']) ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="<?= sanitize($user_data['email']) ?>" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="tel" name="phone" class="form-control" value="<?= sanitize($user_data['phone']) ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Update Photo & Password</h6>
                </div>
                <div class="card-body">
                     <form method="POST" action="" enctype="multipart/form-data" class="mb-4">
                        <label class="form-label">Change Profile Photo</label>
                        <div class="input-group">
                            <input type="file" name="profile_photo" class="form-control" id="profile_photo">
                            <button class="btn btn-outline-secondary" type="submit" name="update_photo">Upload</button>
                        </div>
                        <div class="form-text">Max 1MB. JPG, PNG.</div>
                    </form>
                    <hr>
                    <form method="POST" action="">
                         <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" name="current_password" class="form-control">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" name="new_password" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" name="confirm_password" class="form-control">
                            </div>
                        </div>
                        <button type="submit" name="change_password" class="btn btn-warning">Change Password</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/includes/footer.php'; ?>

<!-- Page-specific scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // QR Code Generation
    const qrCodeElement = document.getElementById('profileQrCode');
    if (qrCodeElement) {
        const qrValue = qrCodeElement.getAttribute('data-value');
        if (qrValue) {
            new QRCode(qrCodeElement, {
                text: qrValue,
                width: 128,
                height: 128,
                colorDark : "#000000",
                colorLight : "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
        }
    }

    // AJAX for Profile Info Form
    $('#profileInfoForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        const messagesDiv = $('#profile-info-messages');

        messagesDiv.html('<div class="alert alert-info">Saving...</div>');

        $.ajax({
            url: '<?= BASE_URL ?>/api/update_profile.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                let alertClass = response.status === 'success' ? 'alert-success' : 'alert-danger';
                messagesDiv.html(`<div class="alert ${alertClass}">${response.message}</div>`);
            },
            error: function() {
                messagesDiv.html('<div class="alert alert-danger">An error occurred.</div>');
            }
        });
    });

    // Add similar AJAX handlers for password and photo forms here...
});
</script>

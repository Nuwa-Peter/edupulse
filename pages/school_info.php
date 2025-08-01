<?php
/**
 * EduPulse - School Information Page
 *
 * Displays details about the user's school, including a map.
 * Allows Headteachers to edit their school's information.
 */

$page_title = "School Information";
check_permission(['Headteacher', 'Deputy Headteacher', 'DOS', 'Bursar', 'Teacher']); // All staff can view
$currentUser = get_current_user();
$school_id = $currentUser['school_id'];

$error_message = '';
$success_message = '';

// Handle form submission for edits
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $currentUser['role'] === 'Headteacher') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error_message = "Invalid request.";
    } else {
        // Sanitize and update school info
        $school_name = sanitize($_POST['school_name']);
        $school_motto = sanitize($_POST['motto']);
        $school_address = sanitize($_POST['address']);
        $school_email = filter_input(INPUT_POST, 'contact_email', FILTER_SANITIZE_EMAIL);
        $school_phone = sanitize($_POST['contact_phone']);
        $latitude = filter_input(INPUT_POST, 'latitude', FILTER_VALIDATE_FLOAT);
        $longitude = filter_input(INPUT_POST, 'longitude', FILTER_VALIDATE_FLOAT);

        try {
            $stmt = $pdo->prepare(
                "UPDATE schools SET name = :name, motto = :motto, address = :address, contact_email = :email,
                 contact_phone = :phone, latitude = :lat, longitude = :lon WHERE school_id = :id"
            );
            $stmt->execute([
                'name' => $school_name,
                'motto' => $school_motto,
                'address' => $school_address,
                'email' => $school_email,
                'phone' => $school_phone,
                'lat' => $latitude,
                'lon' => $longitude,
                'id' => $school_id
            ]);
            $success_message = "School information updated successfully!";
        } catch (PDOException $e) {
            $error_message = "Failed to update information. Please try again.";
            // error_log($e->getMessage());
        }
    }
}

// Fetch school information
try {
    $stmt = $pdo->prepare("SELECT * FROM schools WHERE school_id = :id");
    $stmt->execute(['id' => $school_id]);
    $school = $stmt->fetch();
    if (!$school) {
        die("Could not find school information.");
    }
} catch (PDOException $e) {
    die("Database error while fetching school info.");
}


require_once APP_ROOT . '/includes/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= sanitize($school['name']) ?></h1>
        <?php if ($currentUser['role'] === 'Headteacher'): ?>
            <button id="editSchoolBtn" class="btn btn-primary"><i class="fas fa-edit me-2"></i>Edit Information</button>
        <?php endif; ?>
    </div>

    <?php if ($error_message) echo "<div class='alert alert-danger'>$error_message</div>"; ?>
    <?php if ($success_message) echo "<div class='alert alert-success'>$success_message</div>"; ?>

    <!-- School Info Display -->
    <div class="row">
        <div class="col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">School Details</h6>
                </div>
                <div class="card-body">
                    <p><strong>Motto:</strong> <?= sanitize($school['motto']) ?></p>
                    <p><strong>Level:</strong> <?= sanitize($school['level']) ?></p>
                    <p><strong>Address:</strong> <?= sanitize($school['address']) ?></p>
                    <p><strong>Email:</strong> <?= sanitize($school['contact_email']) ?></p>
                    <p><strong>Phone:</strong> <?= sanitize($school['contact_phone']) ?></p>
                    <p><strong>Established:</strong> <?= sanitize($school['established_year']) ?></p>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Location</h6>
                </div>
                <div class="card-body p-0" style="height: 300px;">
                    <div id="schoolMap" style="height: 100%;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form (Hidden by default) -->
    <?php if ($currentUser['role'] === 'Headteacher'): ?>
    <div id="editSchoolFormContainer" class="card shadow mb-4" style="display: none;">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit School Information</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                <div class="mb-3">
                    <label>School Name</label>
                    <input type="text" name="school_name" class="form-control" value="<?= sanitize($school['name']) ?>">
                </div>
                <div class="mb-3">
                    <label>Motto</label>
                    <input type="text" name="motto" class="form-control" value="<?= sanitize($school['motto']) ?>">
                </div>
                <div class="mb-3">
                    <label>Address</label>
                    <textarea name="address" class="form-control"><?= sanitize($school['address']) ?></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label>Contact Email</label><input type="email" name="contact_email" class="form-control" value="<?= sanitize($school['contact_email']) ?>"></div>
                    <div class="col-md-6 mb-3"><label>Contact Phone</label><input type="tel" name="contact_phone" class="form-control" value="<?= sanitize($school['contact_phone']) ?>"></div>
                </div>
                 <div class="row">
                    <div class="col-md-6 mb-3"><label>Latitude</label><input type="text" id="latitude" name="latitude" class="form-control" value="<?= sanitize($school['latitude']) ?>"></div>
                    <div class="col-md-6 mb-3"><label>Longitude</label><input type="text" id="longitude" name="longitude" class="form-control" value="<?= sanitize($school['longitude']) ?>"></div>
                </div>
                <button type="submit" class="btn btn-success">Save Changes</button>
                <button type="button" id="cancelEditBtn" class="btn btn-secondary">Cancel</button>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once APP_ROOT . '/includes/footer.php'; ?>

<!-- Page-specific scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Leaflet.js Map Initialization
    const lat = <?= json_encode($school['latitude']) ?>;
    const lon = <?= json_encode($school['longitude']) ?>;

    if (lat && lon) {
        const map = L.map('schoolMap').setView([lat, lon], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        L.marker([lat, lon]).addTo(map)
            .bindPopup('<b><?= sanitize($school['name']) ?></b>').openPopup();
    } else {
        document.getElementById('schoolMap').innerHTML = '<div class="alert alert-warning m-3">Location coordinates not set for this school.</div>';
    }

    // Edit Form Toggle
    <?php if ($currentUser['role'] === 'Headteacher'): ?>
    const editBtn = document.getElementById('editSchoolBtn');
    const cancelBtn = document.getElementById('cancelEditBtn');
    const formContainer = document.getElementById('editSchoolFormContainer');

    editBtn.addEventListener('click', () => {
        formContainer.style.display = 'block';
        editBtn.style.display = 'none';
    });

    cancelBtn.addEventListener('click', () => {
        formContainer.style.display = 'none';
        editBtn.style.display = 'block';
    });
    <?php endif; ?>
});
</script>

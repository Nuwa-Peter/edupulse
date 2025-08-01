<?php
/**
 * EduPulse - Teachers Management Page
 *
 * Allows Headteachers to view, add, edit, and manage teacher records.
 */

$page_title = "Manage Teachers";
check_permission(['Headteacher']);
$currentUser = get_current_user();
$school_id = $currentUser['school_id'];

$error_message = '';
$success_message = '';

// Handle form submissions (placeholders)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error_message = "Invalid request.";
    } else {
        if (isset($_POST['add_teacher'])) {
            $success_message = "Teacher added successfully (Placeholder).";
        }
    }
}

// Fetch all teachers for the school
try {
    $stmt = $pdo->prepare(
        "SELECT user_id, edupulse_id, first_name, last_name, email, phone, profile_photo_url
         FROM users
         WHERE school_id = :school_id AND role = 'Teacher'
         ORDER BY last_name, first_name"
    );
    $stmt->execute(['school_id' => $school_id]);
    $teachers = $stmt->fetchAll();
} catch (PDOException $e) {
    $teachers = [];
    $error_message = "Error fetching teachers: " . $e->getMessage();
}

require_once APP_ROOT . '/includes/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manage Teachers</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTeacherModal"><i class="fas fa-user-plus me-2"></i>Add New Teacher</button>
    </div>

    <?php if ($error_message) echo "<div class='alert alert-danger'>$error_message</div>"; ?>
    <?php if ($success_message) echo "<div class='alert alert-success'>$success_message</div>"; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Teacher List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($teachers)): ?>
                            <tr><td colspan="5" class="text-center">No teachers found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($teachers as $teacher): ?>
                            <tr>
                                <td class="text-center"><img src="<?= BASE_URL . '/' . sanitize($teacher['profile_photo_url']) ?>" alt="Photo" class="rounded-circle" width="40" height="40"></td>
                                <td><?= sanitize($teacher['first_name'] . ' ' . $teacher['last_name']) ?></td>
                                <td><?= sanitize($teacher['email']) ?></td>
                                <td><?= sanitize($teacher['phone'] ?? 'N/A') ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>/profile/<?= $teacher['user_id'] ?>" class="btn btn-sm btn-info">View</a>
                                    <button class="btn btn-sm btn-secondary">Subjects</button>
                                    <button class="btn btn-sm btn-warning">Edit</button>
                                    <button class="btn btn-sm btn-danger" onclick="return confirmAction('Are you sure you want to delete this teacher?');">Delete</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Teacher Modal -->
<div class="modal fade" id="addTeacherModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Teacher</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addTeacherForm">
                <div class="modal-body">
                    <div id="teacher-form-messages" class="mb-3"></div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                             <label class="form-label">First Name</label>
                             <input type="text" id="first_name" name="first_name" class="form-control" required>
                        </div>
                         <div class="col-md-6 mb-3">
                             <label class="form-label">Last Name</label>
                             <input type="text" id="last_name" name="last_name" class="form-control" required>
                        </div>
                    </div>
                     <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                        <div class="form-text">An initial password will be generated and sent to this email.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Teacher</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php require_once APP_ROOT . '/includes/footer.php'; ?>

<script>
$(document).ready(function() {
    $('#addTeacherForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        const messagesDiv = $('#teacher-form-messages');

        messagesDiv.html('<div class="alert alert-info">Processing...</div>');

        $.ajax({
            url: '<?= BASE_URL ?>/api/add_teacher.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    messagesDiv.html(`<div class="alert alert-success">${response.message}</div>`);
                    // Optionally, clear form and refresh table after a delay
                    setTimeout(() => location.reload(), 2000);
                } else {
                    messagesDiv.html(`<div class="alert alert-danger">${response.message}</div>`);
                }
            },
            error: function() {
                 messagesDiv.html('<div class="alert alert-danger">An error occurred.</div>');
            }
        });
    });
});
</script>

<?php
/**
 * EduPulse - Students Management Page
 *
 * Allows authorized staff to view, add, edit, and manage student records.
 */

$page_title = "Manage Students";
check_permission(['Headteacher', 'DOS']);
$currentUser = get_current_user();
$school_id = $currentUser['school_id'];

$error_message = '';
$success_message = '';

// Handle form submissions (placeholders)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error_message = "Invalid request.";
    } else {
        if (isset($_POST['add_student'])) {
            // Complex logic for adding a single student and parent, requires a transaction
            $success_message = "Student added successfully (Placeholder).";
        }
        if (isset($_FILES['student_upload'])) {
            // Complex logic for parsing Excel/CSV and bulk inserting students
            $success_message = "Student file uploaded for processing (Placeholder).";
        }
    }
}

// Fetch all students for the school
try {
    $stmt = $pdo->prepare(
        "SELECT u.user_id, u.edupulse_id, u.first_name, u.last_name, u.profile_photo_url, c.class_name
         FROM users u
         JOIN students st ON u.user_id = st.user_id
         LEFT JOIN classes c ON st.class_id = c.class_id
         WHERE u.school_id = :school_id AND u.role = 'Student'
         ORDER BY u.last_name, u.first_name"
    );
    $stmt->execute(['school_id' => $school_id]);
    $students = $stmt->fetchAll();
} catch (PDOException $e) {
    $students = [];
    $error_message = "Error fetching students: " . $e->getMessage();
}

require_once APP_ROOT . '/includes/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manage Students</h1>
        <div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal"><i class="fas fa-user-plus me-2"></i>Add New Student</button>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#uploadStudentModal"><i class="fas fa-file-excel me-2"></i>Bulk Upload</button>
        </div>
    </div>

    <?php if ($error_message) echo "<div class='alert alert-danger'>$error_message</div>"; ?>
    <?php if ($success_message) echo "<div class='alert alert-success'>$success_message</div>"; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Student List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>EduPulse ID</th>
                            <th>Class</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($students)): ?>
                            <tr><td colspan="5" class="text-center">No students found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($students as $student): ?>
                            <tr>
                                <td class="text-center"><img src="<?= BASE_URL . '/' . sanitize($student['profile_photo_url']) ?>" alt="Photo" class="rounded-circle" width="40" height="40"></td>
                                <td><?= sanitize($student['first_name'] . ' ' . $student['last_name']) ?></td>
                                <td><?= sanitize($student['edupulse_id']) ?></td>
                                <td><?= sanitize($student['class_name'] ?? 'N/A') ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>/profile/<?= $student['user_id'] ?>" class="btn btn-sm btn-info">View</a>
                                    <button class="btn btn-sm btn-warning">Edit</button>
                                    <?php if ($currentUser['role'] === 'Headteacher'): ?>
                                    <button class="btn btn-sm btn-danger" onclick="return confirmAction('Are you sure you want to delete this student? This cannot be undone.');">Delete</button>
                                    <?php endif; ?>
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

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <p>Please fill out all details for the new student and their parent/guardian.</p>
                    <!-- Comprehensive form for student and parent details would go here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="add_student" class="btn btn-primary">Save Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Upload Students Modal -->
<div class="modal fade" id="uploadStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Upload Students</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                    <p>Upload an Excel (.xlsx) or CSV file with student data. Please use the provided template.</p>
                    <a href="<?= BASE_URL ?>/Uploads/templates/student_template.csv" class="btn btn-sm btn-outline-success mb-3"><i class="fas fa-file-download me-2"></i>Download Template</a>
                    <div class="mb-3">
                        <label for="student_upload" class="form-label">Student Data File</label>
                        <input type="file" class="form-control" id="student_upload" name="student_upload" accept=".xlsx,.csv" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Upload and Process</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/includes/footer.php'; ?>

<!-- You might need a library like DataTables for advanced search and pagination -->
<!-- <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script> -->
<!-- <script>$('#dataTable').DataTable();</script> -->

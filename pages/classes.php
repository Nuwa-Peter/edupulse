<?php
/**
 * EduPulse - Classes Management Page
 *
 * Allows DOS and Headteachers to view, create, edit, and manage classes
 * and assign subjects to them.
 */

$page_title = "Manage Classes";
check_permission(['Headteacher', 'DOS']);
$currentUser = get_current_user();
$school_id = $currentUser['school_id'];

$error_message = '';
$success_message = '';

// Handle form submissions for adding/editing classes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error_message = "Invalid request.";
    } else {
        // Placeholder for add/edit logic
        $success_message = "Class saved successfully (Placeholder).";
    }
}


// Fetch all classes for the school
try {
    $stmt = $pdo->prepare(
        "SELECT c.*, u.first_name, u.last_name, (SELECT COUNT(*) FROM students s WHERE s.class_id = c.class_id) as student_count
         FROM classes c
         LEFT JOIN users u ON c.class_teacher_id = u.user_id
         WHERE c.school_id = :school_id ORDER BY c.class_name"
    );
    $stmt->execute(['school_id' => $school_id]);
    $classes = $stmt->fetchAll();
} catch (PDOException $e) {
    $classes = [];
    $error_message = "Error fetching classes: " . $e->getMessage();
}

// Fetch all teachers for dropdowns
try {
    $teacher_stmt = $pdo->prepare("SELECT user_id, first_name, last_name FROM users WHERE school_id = :school_id AND role = 'Teacher' ORDER BY first_name");
    $teacher_stmt->execute(['school_id' => $school_id]);
    $teachers = $teacher_stmt->fetchAll();
} catch (PDOException $e) {
    $teachers = [];
}


require_once APP_ROOT . '/includes/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manage Classes & Subjects</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#classModal"><i class="fas fa-plus me-2"></i>Add New Class</button>
    </div>

    <?php if ($error_message) echo "<div class='alert alert-danger'>$error_message</div>"; ?>
    <?php if ($success_message) echo "<div class='alert alert-success'>$success_message</div>"; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Class List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Class Name</th>
                            <th>Class Teacher</th>
                            <th>No. of Students</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($classes)): ?>
                            <tr><td colspan="4" class="text-center">No classes found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($classes as $class): ?>
                            <tr>
                                <td><?= sanitize($class['class_name']) ?></td>
                                <td><?= $class['first_name'] ? sanitize($class['first_name'] . ' ' . $class['last_name']) : 'N/A' ?></td>
                                <td><?= $class['student_count'] ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#subjectsModal" data-class-id="<?= $class['class_id'] ?>">Subjects</button>
                                    <button class="btn btn-sm btn-warning">Edit</button>
                                    <button class="btn btn-sm btn-danger">Delete</button>
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

<!-- Add/Edit Class Modal -->
<div class="modal fade" id="classModal" tabindex="-1" aria-labelledby="classModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="classModalLabel">Add New Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                    <input type="hidden" name="class_id" value="">
                    <div class="mb-3">
                        <label for="class_name" class="form-label">Class Name (e.g., P.1, S.5 Arts)</label>
                        <input type="text" class="form-control" id="class_name" name="class_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="class_teacher_id" class="form-label">Assign Class Teacher</label>
                        <select class="form-select" id="class_teacher_id" name="class_teacher_id">
                            <option value="">None</option>
                            <?php foreach ($teachers as $teacher): ?>
                                <option value="<?= $teacher['user_id'] ?>"><?= sanitize($teacher['first_name'] . ' ' . $teacher['last_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="save_class" class="btn btn-primary">Save Class</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Manage Subjects Modal -->
<div class="modal fade" id="subjectsModal" tabindex="-1" aria-labelledby="subjectsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="subjectsModalLabel">Manage Subjects for Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Content will be loaded via AJAX or pre-populated -->
                <p>Subject management for the selected class will appear here. You can assign subjects and teachers for each subject.</p>
            </div>
             <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save Subject Assignments</button>
            </div>
        </div>
    </div>
</div>


<?php require_once APP_ROOT . '/includes/footer.php'; ?>

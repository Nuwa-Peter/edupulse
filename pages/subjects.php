<?php
/**
 * EduPulse - Subjects Management Page
 *
 * Allows DOS and Headteachers to manage the list of subjects offered.
 */

$page_title = "Manage Subjects";
check_permission(['Headteacher', 'DOS']);
$currentUser = get_current_user();
$school_id = $currentUser['school_id'];

$error_message = '';
$success_message = '';

// Handle form submission for adding a new subject
if (isset($_POST['add_subject'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error_message = "Invalid request.";
    } else {
        $subject_name = sanitize($_POST['subject_name']);
        if (empty($subject_name)) {
            $error_message = "Subject name cannot be empty.";
        } else {
            try {
                // Check if subject already exists
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM subjects WHERE subject_name = ?");
                $stmt->execute([$subject_name]);
                if ($stmt->fetchColumn() > 0) {
                    $error_message = "This subject already exists.";
                } else {
                    $insert_stmt = $pdo->prepare("INSERT INTO subjects (subject_name) VALUES (?)");
                    $insert_stmt->execute([$subject_name]);
                    $success_message = "Subject '" . $subject_name . "' added successfully.";
                }
            } catch (PDOException $e) {
                $error_message = "Failed to add subject.";
            }
        }
    }
}


// Fetch all subjects
try {
    $stmt = $pdo->query("SELECT * FROM subjects ORDER BY subject_name");
    $subjects = $stmt->fetchAll();
} catch (PDOException $e) {
    $subjects = [];
    $error_message = "Error fetching subjects: " . $e->getMessage();
}

require_once APP_ROOT . '/includes/header.php';
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Manage Subjects</h1>

    <?php if ($error_message) echo "<div class='alert alert-danger'>$error_message</div>"; ?>
    <?php if ($success_message) echo "<div class='alert alert-success'>$success_message</div>"; ?>

    <div class="row">
        <!-- Add Subject Form -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Add New Subject</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                        <div class="mb-3">
                            <label for="subject_name" class="form-label">Subject Name</label>
                            <input type="text" class="form-control" id="subject_name" name="subject_name" required>
                        </div>
                        <button type="submit" name="add_subject" class="btn btn-primary">Add Subject</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Subject List -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Available Subjects</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Subject Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($subjects)): ?>
                                    <tr><td colspan="2" class="text-center">No subjects found.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($subjects as $subject): ?>
                                    <tr>
                                        <td><?= sanitize($subject['subject_name']) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-warning">Edit</button>
                                            <button class="btn btn-sm btn-danger" onclick="return confirmAction('Are you sure? Deleting a subject can affect existing records.');">Delete</button>
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
    </div>
</div>

<?php require_once APP_ROOT . '/includes/footer.php'; ?>

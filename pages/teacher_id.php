<?php
/**
 * EduPulse - Teacher ID Card Generation Page
 *
 * Allows authorized users to generate and download teacher ID cards as PDFs.
 */

$page_title = "Generate Teacher ID Cards";
check_permission(['DOS', 'Teacher']);
$currentUser = get_current_user();
$school_id = $currentUser['school_id'];

// Handle PDF generation request
if (isset($_POST['generate_id'])) {
    // TCPDF logic for teacher ID cards would go here.
    $success_message = "Teacher ID Card generation initiated (Placeholder).";
}


// Fetch teachers for the dropdown
try {
    $stmt = $pdo->prepare("SELECT user_id, first_name, last_name FROM users WHERE school_id = :school_id AND role = 'Teacher' ORDER BY last_name");
    $stmt->execute(['school_id' => $school_id]);
    $teachers = $stmt->fetchAll();
} catch (PDOException $e) {
    $teachers = [];
    $error_message = "Error fetching teachers.";
}

require_once APP_ROOT . '/includes/header.php';
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Generate Teacher ID Cards</h1>

    <?php if (isset($error_message)) echo "<div class='alert alert-danger'>$error_message</div>"; ?>
    <?php if (isset($success_message)) echo "<div class='alert alert-success'>$success_message</div>"; ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Select Teacher</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                        <div class="row align-items-end">
                            <div class="col-md-8 mb-3">
                                <label for="teacher_id" class="form-label">Teacher</label>
                                <select id="teacher_id" name="teacher_id" class="form-select" required>
                                    <option value="">Choose a teacher...</option>
                                    <?php foreach ($teachers as $teacher): ?>
                                        <option value="<?= $teacher['user_id'] ?>"><?= sanitize($teacher['first_name'] . ' ' . $teacher['last_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <button type="submit" name="generate_id" class="btn btn-primary w-100">Generate ID Card</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
             <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">ID Card Preview</h6>
                </div>
                <div class="card-body text-center">
                    <p class="text-muted">A preview of the generated ID card will appear here.</p>
                    <img src="https://via.placeholder.com/250x150.png?text=ID+Card+Front" alt="ID Card Preview" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/includes/footer.php'; ?>

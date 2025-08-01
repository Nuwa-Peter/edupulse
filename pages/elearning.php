<?php
/**
 * EduPulse - E-Learning Page
 *
 * A repository for teachers to upload and students/parents to view learning materials.
 */

$page_title = "E-Learning";
check_permission(['Teacher', 'Student', 'Parent', 'DOS']);
$currentUser = get_current_user();
$school_id = $currentUser['school_id'];
$userRole = $currentUser['role'];

// Handle file upload
if (isset($_POST['upload_material']) && $userRole === 'Teacher') {
    // Secure file upload and database insertion logic would go here.
    $success_message = "Material uploaded successfully (Placeholder).";
}


// Fetch e-learning materials
try {
    // This could be filtered by class/subject based on GET parameters
    $stmt = $pdo->prepare(
        "SELECT e.*, s.subject_name, c.class_name, u.first_name, u.last_name
         FROM elearning_materials e
         JOIN subjects s ON e.subject_id = s.subject_id
         JOIN classes c ON e.class_id = c.class_id
         JOIN users u ON e.teacher_id = u.user_id
         WHERE c.school_id = :school_id
         ORDER BY e.uploaded_at DESC"
    );
    $stmt->execute(['school_id' => $school_id]);
    $materials = $stmt->fetchAll();
} catch (PDOException $e) {
    $materials = [];
    $error_message = "Error fetching materials.";
}

require_once APP_ROOT . '/includes/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">E-Learning Materials</h1>
        <?php if ($userRole === 'Teacher'): ?>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal"><i class="fas fa-upload me-2"></i>Upload New Material</button>
        <?php endif; ?>
    </div>

    <?php if (isset($error_message)) echo "<div class='alert alert-danger'>$error_message</div>"; ?>
    <?php if (isset($success_message)) echo "<div class='alert alert-success'>$success_message</div>"; ?>

    <!-- Filter Bar -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <!-- Filter dropdowns for class and subject would go here -->
            <p class="text-muted">Filter controls will be here.</p>
        </div>
    </div>

    <!-- Materials Grid -->
    <div class="row">
        <?php if (empty($materials)): ?>
            <div class="col-12"><p class="text-center">No e-learning materials found.</p></div>
        <?php else: ?>
            <?php foreach ($materials as $material): ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="fs-1 me-3">
                                <i class="fas <?= $material['file_type'] === 'PDF' ? 'fa-file-pdf text-danger' : 'fa-file-video text-info' ?>"></i>
                            </div>
                            <div>
                                <h5 class="card-title"><?= sanitize($material['title']) ?></h5>
                                <h6 class="card-subtitle mb-2 text-muted"><?= sanitize($material['subject_name']) ?> - <?= sanitize($material['class_name']) ?></h6>
                                <p class="card-text small"><?= sanitize($material['description']) ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 d-flex justify-content-between align-items-center">
                        <small class="text-muted">By: <?= sanitize($material['first_name']) ?></small>
                        <a href="<?= BASE_URL . '/' . sanitize($material['file_path']) ?>" class="btn btn-sm btn-outline-primary" target="_blank">View/Download</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>


<!-- Upload Modal (for Teachers) -->
<?php if ($userRole === 'Teacher'): ?>
<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload New Material</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                     <div class="mb-3">
                        <label class="form-label">File (PDF or MP4, max 10MB)</label>
                        <input type="file" name="material_file" class="form-control" accept=".pdf,.mp4" required>
                    </div>
                    <!-- Dropdowns for class and subject would go here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="upload_material" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once APP_ROOT . '/includes/footer.php'; ?>

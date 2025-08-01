<?php
/**
 * EduPulse - Announcements Page
 *
 * Displays school-wide announcements and allows authorized users to create new ones.
 */

$page_title = "Announcements";
check_permission(['Superadmin', 'Headteacher', 'Deputy Headteacher', 'DOS', 'Bursar', 'Teacher', 'Student', 'Parent']);
$currentUser = get_current_user();
$school_id = $currentUser['school_id'];
$userRole = $currentUser['role'];

// Handle form submission for creating an announcement
if (isset($_POST['create_announcement']) && ($userRole === 'Headteacher' || $userRole === 'Superadmin')) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error_message = "Invalid request.";
    } else {
        // Logic to insert announcement into the database
        // and trigger email/SMS notifications would go here.
        $success_message = "Announcement posted successfully (Placeholder).";
    }
}


// Fetch relevant announcements
try {
    $stmt = $pdo->prepare(
        "SELECT a.*, u.first_name, u.last_name
         FROM announcements a
         JOIN users u ON a.user_id = u.user_id
         WHERE a.school_id = :school_id AND (a.target_role = 'All' OR a.target_role = :user_role)
         ORDER BY a.created_at DESC"
    );
    $stmt->execute(['school_id' => $school_id, 'user_role' => $userRole]);
    $announcements = $stmt->fetchAll();
} catch (PDOException $e) {
    $announcements = [];
    $error_message = "Error fetching announcements.";
}

require_once APP_ROOT . '/includes/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">E-Notice Board</h1>
        <?php if ($userRole === 'Headteacher' || $userRole === 'Superadmin'): ?>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAnnouncementModal"><i class="fas fa-plus-circle me-2"></i>Create Announcement</button>
        <?php endif; ?>
    </div>

    <?php if (isset($error_message)) echo "<div class='alert alert-danger'>$error_message</div>"; ?>
    <?php if (isset($success_message)) echo "<div class='alert alert-success'>$success_message</div>"; ?>

    <?php if (empty($announcements)): ?>
        <div class="card shadow">
            <div class="card-body text-center">
                <p class="lead text-muted">No announcements to display at the moment.</p>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($announcements as $announcement): ?>
        <div class="card shadow mb-3">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary"><?= sanitize($announcement['title']) ?></h6>
                <div class="text-muted small">
                    Posted by <?= sanitize($announcement['first_name'] . ' ' . $announcement['last_name']) ?> on <?= date('M d, Y', strtotime($announcement['created_at'])) ?>
                </div>
            </div>
            <div class="card-body">
                <?= nl2br(sanitize($announcement['content'])) // nl2br to respect line breaks ?>
            </div>
            <div class="card-footer text-muted small">
                Audience: <?= sanitize($announcement['target_role']) ?>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

<!-- Create Announcement Modal -->
<?php if ($userRole === 'Headteacher' || $userRole === 'Superadmin'): ?>
<div class="modal fade" id="createAnnouncementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Announcement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea name="content" class="form-control" rows="5" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="target_role" class="form-label">Target Audience</label>
                        <select name="target_role" class="form-select" required>
                            <option value="All">All Users</option>
                            <option value="Teachers">Teachers Only</option>
                            <option value="Parents">Parents Only</option>
                            <option value="Students">Students Only</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="create_announcement" class="btn btn-primary">Post Announcement</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once APP_ROOT . '/includes/footer.php'; ?>

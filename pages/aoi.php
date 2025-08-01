<?php
/**
 * EduPulse - Activities of Integration (AOI) Page
 *
 * Manages extracurricular activities like clubs and sports.
 */

$page_title = "Activities of Integration";
check_permission(['DOS', 'Headteacher', 'Teacher', 'Student']);
$currentUser = get_current_user();
$school_id = $currentUser['school_id'];
$userRole = $currentUser['role'];

// Fetch AOIs for the school
try {
    $stmt = $pdo->prepare(
        "SELECT a.*, u.first_name, u.last_name
         FROM aoi a
         LEFT JOIN users u ON a.teacher_in_charge_id = u.user_id
         WHERE a.school_id = :school_id
         ORDER BY a.name"
    );
    $stmt->execute(['school_id' => $school_id]);
    $aois = $stmt->fetchAll();
} catch (PDOException $e) {
    $aois = [];
    $error_message = "Error fetching activities.";
}

require_once APP_ROOT . '/includes/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Activities of Integration (Clubs & Sports)</h1>
        <?php if ($userRole === 'DOS' || $userRole === 'Headteacher'): ?>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAoiModal"><i class="fas fa-plus me-2"></i>Create New Activity</button>
        <?php endif; ?>
    </div>

    <div class="row">
        <?php if (empty($aois)): ?>
            <div class="col-12"><p class="text-center">No activities have been set up yet.</p></div>
        <?php else: ?>
            <?php foreach ($aois as $aoi): ?>
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <h5 class="card-title text-primary"><?= sanitize($aoi['name']) ?></h5>
                        <p class="card-text"><?= sanitize($aoi['description']) ?></p>
                    </div>
                    <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">In Charge: </small>
                            <strong><?= sanitize($aoi['first_name'] . ' ' . $aoi['last_name']) ?></strong>
                        </div>
                        <div>
                            <?php if ($userRole === 'Student'): ?>
                                <button class="btn btn-sm btn-success">Join</button>
                            <?php elseif ($userRole === 'Teacher' && $aoi['teacher_in_charge_id'] === $currentUser['user_id']): ?>
                                <button class="btn btn-sm btn-info">Manage</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Create AOI Modal -->
<?php if ($userRole === 'DOS' || $userRole === 'Headteacher'): ?>
<div class="modal fade" id="createAoiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Activity</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                    <div class="mb-3">
                        <label class="form-label">Activity Name</label>
                        <input type="text" name="aoi_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="aoi_description" class="form-control" rows="3"></textarea>
                    </div>
                    <!-- Dropdown to select teacher in charge would go here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="create_aoi" class="btn btn-primary">Create Activity</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once APP_ROOT . '/includes/footer.php'; ?>

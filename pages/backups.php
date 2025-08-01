<?php
/**
 * EduPulse - System Backups Page
 *
 * Allows the Superadmin to create and manage database backups.
 */

$page_title = "System Backups";
check_permission(['Superadmin']);

$backup_dir = APP_ROOT . '/Uploads/backups/';
$backups = [];
if (is_dir($backup_dir)) {
    $files = array_diff(scandir($backup_dir), array('.', '..'));
    foreach ($files as $file) {
        if (is_file($backup_dir . $file)) {
            $backups[] = [
                'name' => $file,
                'size' => filesize($backup_dir . $file),
                'date' => filemtime($backup_dir . $file)
            ];
        }
    }
    // Sort by date descending
    usort($backups, fn($a, $b) => $b['date'] <=> $a['date']);
}

// Handle backup creation/deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error_message = "Invalid request.";
    } else {
        if(isset($_POST['create_backup'])) {
            // Placeholder for the backup creation logic.
            // This is a server-intensive task and should be handled carefully.
            // e.g., exec('mysqldump ... > ' . $backup_dir . 'new_backup.sql');
            $success_message = "Backup creation process started (Placeholder).";
        }
    }
}


require_once APP_ROOT . '/includes/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Database Backups</h1>
        <form method="POST" action="" class="d-inline">
             <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
             <button type="submit" name="create_backup" class="btn btn-primary"><i class="fas fa-plus-circle me-2"></i>Create New Backup</button>
        </form>
    </div>

    <?php if (isset($error_message)) echo "<div class='alert alert-danger'>$error_message</div>"; ?>
    <?php if (isset($success_message)) echo "<div class='alert alert-success'>$success_message</div>"; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Existing Backups</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Filename</th>
                            <th>Size</th>
                            <th>Date Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($backups)): ?>
                            <tr><td colspan="4" class="text-center">No backups found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($backups as $backup): ?>
                            <tr>
                                <td><i class="fas fa-database me-2"></i><?= sanitize($backup['name']) ?></td>
                                <td><?= round($backup['size'] / 1024, 2) ?> KB</td>
                                <td><?= date('Y-m-d H:i:s', $backup['date']) ?></td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-success">Download</a>
                                    <a href="#" class="btn btn-sm btn-danger" onclick="return confirmAction('Are you sure you want to delete this backup?');">Delete</a>
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

<?php require_once APP_ROOT . '/includes/footer.php'; ?>

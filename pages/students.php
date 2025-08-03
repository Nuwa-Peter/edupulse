<?php
/**
 * EduPulse - Student Management
 */

require_once __DIR__ . '/../includes/config.php';

// --- Authentication Check ---
if (!is_logged_in() || !in_array(get_current_user()['role'], ['Headteacher', 'DOS', 'Admin'])) {
    redirect('/index.php?route=login');
}

$page_title = "Student Management";
require_once APP_ROOT . '/includes/header.php';

// Fetch students from the database
$school_id = get_current_user()['school_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE role = 'Student' AND school_id = :school_id ORDER BY last_name, first_name");
$stmt->execute(['school_id' => $school_id]);
$students = $stmt->fetchAll();

?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Student Management</h1>
    <div>
        <a href="#" class="btn btn-sm btn-success shadow-sm"><i class="fas fa-file-excel fa-sm"></i> Import Students</a>
        <a href="#" class="btn btn-sm btn-info shadow-sm"><i class="fas fa-file-excel fa-sm"></i> Export Students</a>
        <a href="#" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm"></i> Add New Student</a>
    </div>
</div>

<?php display_flash_message('success', 'alert-success'); ?>
<?php display_flash_message('error', 'alert-danger'); ?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">All Students</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>EduPulse ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($students)): ?>
                        <tr>
                            <td colspan="6" class="text-center">No students found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?= sanitize($student['edupulse_id']) ?></td>
                                <td><?= sanitize($student['first_name'] . ' ' . $student['last_name']) ?></td>
                                <td><?= sanitize($student['email'] ?? 'N/A') ?></td>
                                <td><?= sanitize($student['phone'] ?? 'N/A') ?></td>
                                <td><span class="badge bg-<?= $student['status'] === 'active' ? 'success' : 'danger' ?>"><?= ucfirst(sanitize($student['status'])) ?></span></td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-info" title="View Profile"><i class="fas fa-eye"></i></a>
                                    <a href="#" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                    <a href="#" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this student?');"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<?php
require_once APP_ROOT . '/includes/footer.php';
?>

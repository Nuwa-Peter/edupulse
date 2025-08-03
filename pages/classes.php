<?php
/**
 * EduPulse - Class Management
 */

require_once __DIR__ . '/../includes/config.php';

// --- Authentication Check ---
if (!is_logged_in() || !in_array(get_current_user()['role'], ['Headteacher', 'DOS', 'Admin'])) {
    redirect('/index.php?route=login');
}

$page_title = "Class Management";
require_once APP_ROOT . '/includes/header.php';

// This is a placeholder for where you would fetch and display classes.
// For now, we will use a static array.
$classes = [
    ['class_id' => 1, 'name' => 'P.1', 'stream' => 'Blue', 'teacher' => 'Jane Smith'],
    ['class_id' => 2, 'name' => 'P.2', 'stream' => 'Green', 'teacher' => 'John Doe'],
    ['class_id' => 3, 'name' => 'S.1', 'stream' => 'A', 'teacher' => 'Peter Jones'],
];

?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Class Management</h1>
    <div>
        <a href="#" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm"></i> Add New Class</a>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">All Classes</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Class Name</th>
                        <th>Stream</th>
                        <th>Class Teacher</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($classes as $class): ?>
                        <tr>
                            <td><?= sanitize($class['name']) ?></td>
                            <td><?= sanitize($class['stream']) ?></td>
                            <td><?= sanitize($class['teacher']) ?></td>
                            <td>
                                <a href="#" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                <a href="#" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this class?');"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<?php
require_once APP_ROOT . '/includes/footer.php';
?>

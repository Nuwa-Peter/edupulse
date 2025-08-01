<?php
/**
 * EduPulse - Grading Scales Management Page
 *
 * Allows administrators to define how numerical marks are converted to grades.
 */

$page_title = "Manage Grading Scales";
check_permission(['Headteacher', 'DOS']);

$error_message = '';
$success_message = '';

// Handle form submissions (placeholder)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error_message = "Invalid request.";
    } else {
        $success_message = "Grading scale updated successfully (Placeholder).";
    }
}

// Fetch all grading scales
try {
    $stmt = $pdo->query("SELECT * FROM grading_scales ORDER BY school_level, max_score DESC");
    $all_grades = $stmt->fetchAll();

    // Separate into Primary and Secondary
    $primary_grades = array_filter($all_grades, fn($g) => $g['school_level'] === 'Primary');
    $secondary_grades = array_filter($all_grades, fn($g) => $g['school_level'] === 'Secondary');

} catch (PDOException $e) {
    $primary_grades = [];
    $secondary_grades = [];
    $error_message = "Error fetching grading scales: " . $e->getMessage();
}

require_once APP_ROOT . '/includes/header.php';
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Manage Grading Scales</h1>

    <?php if ($error_message) echo "<div class='alert alert-danger'>$error_message</div>"; ?>
    <?php if ($success_message) echo "<div class='alert alert-success'>$success_message</div>"; ?>

    <div class="row">
        <!-- Add Grade Form -->
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Add New Grade Entry</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="" class="row g-3 align-items-end">
                        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                        <div class="col-md-2">
                            <label class="form-label">School Level</label>
                            <select name="school_level" class="form-select" required>
                                <option value="Primary">Primary</option>
                                <option value="Secondary">Secondary</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Grade Name</label>
                            <input type="text" name="grade_name" class="form-control" placeholder="e.g., A, Excellent" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Min Score</label>
                            <input type="number" name="min_score" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Max Score</label>
                            <input type="number" name="max_score" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Comment</label>
                            <input type="text" name="comment" class="form-control" placeholder="e.g., Good effort">
                        </div>
                        <div class="col-md-1">
                            <button type="submit" name="add_grade" class="btn btn-primary">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Grading Scales Display -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Primary School Grading Scale</h6>
                </div>
                <div class="card-body">
                    <?php render_grade_table($primary_grades); ?>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Secondary School (UNEB) Grading Scale</h6>
                </div>
                <div class="card-body">
                    <?php render_grade_table($secondary_grades); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Helper function to render the grade table to avoid repetition
function render_grade_table($grades) {
    if (empty($grades)) {
        echo '<p class="text-center">No grades defined for this level.</p>';
        return;
    }
    echo '<div class="table-responsive">';
    echo '<table class="table table-bordered table-striped" width="100%" cellspacing="0">';
    echo '<thead><tr><th>Grade</th><th>Score Range</th><th>Comment</th><th>Actions</th></tr></thead>';
    echo '<tbody>';
    foreach ($grades as $grade) {
        echo '<tr>';
        echo '<td>' . sanitize($grade['grade_name']) . '</td>';
        echo '<td>' . sanitize($grade['min_score']) . ' - ' . sanitize($grade['max_score']) . '</td>';
        echo '<td>' . sanitize($grade['comment']) . '</td>';
        echo '<td>
                <button class="btn btn-sm btn-warning py-0 px-1"><i class="fas fa-edit"></i></button>
                <button class="btn btn-sm btn-danger py-0 px-1"><i class="fas fa-trash"></i></button>
              </td>';
        echo '</tr>';
    }
    echo '</tbody></table></div>';
}

require_once APP_ROOT . '/includes/footer.php';
?>

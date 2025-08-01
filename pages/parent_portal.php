<?php
/**
 * EduPulse - Parent Portal Page
 *
 * A dedicated dashboard for parents to view their child's academic progress.
 */

$page_title = "Parent Portal";
check_permission(['Parent']);
$currentUser = get_current_user();
$parent_user_id = $_SESSION['user_id'];

// Fetch the parent's child/children
// This placeholder assumes the parent has one child. A real implementation would loop.
try {
    $stmt = $pdo->prepare(
       "SELECT u.user_id, u.first_name, u.last_name, c.class_name
        FROM users u
        JOIN students s ON u.user_id = s.user_id
        JOIN student_parent_relations r ON u.user_id = r.student_user_id
        LEFT JOIN classes c ON s.class_id = c.class_id
        WHERE r.parent_user_id = :parent_id"
    );
    $stmt->execute(['parent_id' => $parent_user_id]);
    $child = $stmt->fetch();
} catch (PDOException $e) {
    $child = null;
    $error_message = "Could not fetch your child's information.";
}

// Fetch summary data (placeholders)
$recent_marks = [
    ['subject' => 'Mathematics', 'score' => 85],
    ['subject' => 'English', 'score' => 92]
];
$fee_balance = 250000;
$attendance_summary = "98% Present";

require_once APP_ROOT . '/includes/header.php';
?>

<div class="container-fluid">
    <?php if (!$child): ?>
        <div class="alert alert-warning">Your account is not yet linked to a student. Please contact the school administrator.</div>
    <?php else: ?>
    <h1 class="h3 mb-4 text-gray-800">Dashboard for <?= sanitize($child['first_name'] . ' ' . $child['last_name']) ?></h1>

    <div class="row">
        <!-- Attendance Summary -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Attendance (Term)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $attendance_summary ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-user-check fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Fee Balance -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Outstanding Fees</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= format_currency($fee_balance) ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-file-invoice-dollar fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">Recent Grades</h6></div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <?php foreach($recent_marks as $mark): ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><?= $mark['subject'] ?></span>
                            <span class="fw-bold"><?= $mark['score'] ?>%</span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
             <div class="card shadow mb-4">
                <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">Quick Links</h6></div>
                <div class="card-body">
                   <a href="#" class="btn btn-outline-primary mb-2 d-block">View Full Report Card</a>
                   <a href="#" class="btn btn-outline-info mb-2 d-block">View Attendance History</a>
                   <a href="#" class="btn btn-outline-success d-block">Make a Fee Payment</a>
                </div>
            </div>
        </div>
    </div>

    <?php endif; ?>
</div>

<?php require_once APP_ROOT . '/includes/footer.php'; ?>

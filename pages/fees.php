<?php
/**
 * EduPulse - Fee Management Page
 *
 * Allows Bursars and Headteachers to manage student fees and record payments.
 * Parents can view their child's fee status.
 */

$page_title = "Fee Management";
check_permission(['Bursar', 'Headteacher', 'Parent']);
$currentUser = get_current_user();
$school_id = $currentUser['school_id'];

// Fetch fee data for all students (Bursar/HT view)
// In a real app, this would be paginated.
try {
    $stmt = $pdo->prepare(
        "SELECT f.*, u.first_name, u.last_name, c.class_name
         FROM fees f
         JOIN students s ON f.student_id = s.student_id
         JOIN users u ON s.user_id = u.user_id
         JOIN classes c ON s.class_id = c.class_id
         WHERE u.school_id = :school_id AND f.academic_year = :year AND f.term = :term"
    );
    // Using placeholders for current term/year
    $stmt->execute(['school_id' => $school_id, 'year' => date('Y'), 'term' => '1']);
    $fee_records = $stmt->fetchAll();
} catch (PDOException $e) {
    $fee_records = [];
    $error_message = "Error fetching fee records.";
}

require_once APP_ROOT . '/includes/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Student Fee Management</h1>
        <?php if ($currentUser['role'] !== 'Parent'): ?>
        <div>
            <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#setFeesModal">Set Term Fees</button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#recordPaymentModal">Record Payment</button>
        </div>
        <?php endif; ?>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Fee Status for Term 1, <?= date('Y') ?></h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Class</th>
                            <th>Total Due</th>
                            <th>Total Paid</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($fee_records as $record): ?>
                        <tr>
                            <td><?= sanitize($record['first_name'] . ' ' . $record['last_name']) ?></td>
                            <td><?= sanitize($record['class_name']) ?></td>
                            <td><?= format_currency($record['total_due']) ?></td>
                            <td><?= format_currency($record['total_paid']) ?></td>
                            <td class="fw-bold <?= $record['balance'] > 0 ? 'text-danger' : 'text-success' ?>">
                                <?= format_currency($record['balance']) ?>
                            </td>
                            <td><span class="badge bg-<?= $record['status'] === 'Paid' ? 'success' : ($record['status'] === 'Partially Paid' ? 'warning' : 'danger') ?>">
                                <?= sanitize($record['status']) ?>
                            </span></td>
                            <td>
                                <button class="btn btn-sm btn-secondary">View Details</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modals for Bursar/HT -->
<?php if ($currentUser['role'] !== 'Parent'): ?>
<!-- Record Payment Modal -->
<div class="modal fade" id="recordPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Record a Payment</h5></div>
            <div class="modal-body">
                <p>Form to select a student, enter payment amount and method.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save Payment</button>
            </div>
        </div>
    </div>
</div>
<!-- Set Term Fees Modal -->
<div class="modal fade" id="setFeesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Set Fees for a Term</h5></div>
            <div class="modal-body">
                <p>Form to select a class, term, year, and enter the total fee amount.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Apply Fees</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once APP_ROOT . '/includes/footer.php'; ?>

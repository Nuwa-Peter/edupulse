<?php
/**
 * EduPulse - Fee Management
 */

require_once __DIR__ . '/../includes/config.php';

// --- Authentication Check ---
if (!is_logged_in() || !in_array(get_current_user()['role'], ['Headteacher', 'Bursar'])) {
    redirect('/index.php?route=login');
}

$page_title = "Fee Management";
require_once APP_ROOT . '/includes/header.php';

// Placeholder data
$students = [
    ['id' => 4, 'name' => 'Peter Jones', 'fee_balance' => 150000],
    ['id' => 5, 'name' => 'Aisha Nakato', 'fee_balance' => 0],
];

?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Fee Management</h1>
    <div>
        <a href="#" class="btn btn-sm btn-success shadow-sm"><i class="fas fa-file-excel fa-sm"></i> Import Payments</a>
        <a href="#" class="btn btn-sm btn-info shadow-sm"><i class="fas fa-file-excel fa-sm"></i> Export Ledger</a>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Student Fee Balances</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Fee Balance (UGX)</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?= sanitize($student['name']) ?></td>
                            <td><?= format_currency($student['fee_balance']) ?></td>
                            <td>
                                <?php if ($student['fee_balance'] <= 0): ?>
                                    <span class="badge bg-success">Cleared</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Has Balance</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="#" class="btn btn-sm btn-primary" title="Add Payment"><i class="fas fa-plus"></i> Add Payment</a>
                                <a href="#" class="btn btn-sm btn-info" title="View Ledger"><i class="fas fa-eye"></i> View Ledger</a>
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

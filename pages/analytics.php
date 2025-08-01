<?php
/**
 * EduPulse - Analytics & AI Insights Page
 *
 * Provides data visualizations for school performance and AI-driven predictions.
 */

$page_title = "Analytics & Insights";
check_permission(['Headteacher', 'Teacher']);
$currentUser = get_current_user();
$userRole = $currentUser['role'];

// Placeholder data for charts. In a real app, this would be fetched from the DB.
$performance_data = [
    'labels' => ['Math', 'English', 'Science', 'History'],
    'scores' => [75, 82, 68, 79]
];

$fee_data = [
    'labels' => ['Paid', 'Partially Paid', 'Unpaid'],
    'amounts' => [150000000, 45000000, 25000000]
];

// Fetch AI predictions
try {
    // This would be filtered by school_id
    $stmt = $pdo->query("SELECT p.*, u.first_name, u.last_name FROM ai_predictions p JOIN students s ON p.student_id = s.student_id JOIN users u ON s.user_id = u.user_id ORDER BY p.generated_at DESC");
    $predictions = $stmt->fetchAll();
} catch (PDOException $e) {
    $predictions = [];
    $error_message = "Could not fetch AI predictions.";
}


require_once APP_ROOT . '/includes/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Analytics Dashboard</h1>
        <?php if ($userRole === 'Headteacher'): ?>
        <button class="btn btn-primary"><i class="fas fa-robot me-2"></i>Run New AI Analysis</button>
        <?php endif; ?>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Average Performance by Subject</h6>
                </div>
                <div class="card-body">
                    <canvas id="performanceBarChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Fee Collection Status</h6>
                </div>
                <div class="card-body">
                    <canvas id="feesPieChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Insights -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">AI-Powered Student Insights</h6>
        </div>
        <div class="card-body">
            <p>The following students have been identified by the AI model based on recent performance and engagement data.</p>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr><th>Student Name</th><th>Prediction</th><th>Confidence</th><th>Details</th></tr>
                    </thead>
                    <tbody>
                        <?php if(empty($predictions)): ?>
                            <tr><td colspan="4" class="text-center">No AI predictions available. Run a new analysis.</td></tr>
                        <?php else: ?>
                            <?php foreach($predictions as $pred): ?>
                            <tr>
                                <td><?= sanitize($pred['first_name'] . ' ' . $pred['last_name']) ?></td>
                                <td><span class="badge bg-<?= $pred['prediction_type'] === 'at-risk' ? 'danger' : 'success' ?>">
                                    <?= ucfirst(str_replace('-', ' ', $pred['prediction_type'])) ?>
                                </span></td>
                                <td><?= round($pred['confidence_score'] * 100, 1) ?>%</td>
                                <td><?= sanitize($pred['details']) ?></td>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Performance Chart
    new Chart($('#performanceBarChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($performance_data['labels']) ?>,
            datasets: [{
                label: 'Average Score',
                data: <?= json_encode($performance_data['scores']) ?>,
                backgroundColor: 'rgba(78, 115, 223, 0.8)'
            }]
        },
        options: {
            scales: { y: { beginAtZero: true, max: 100 } },
            plugins: { legend: { display: false } }
        }
    });

    // Fees Chart
    new Chart($('#feesPieChart'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($fee_data['labels']) ?>,
            datasets: [{
                data: <?= json_encode($fee_data['amounts']) ?>,
                backgroundColor: ['#1cc88a', '#f6c23e', '#e74a3b']
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
});
</script>

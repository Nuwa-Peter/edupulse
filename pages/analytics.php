<?php
/**
 * EduPulse - Analytics & AI Insights
 */

require_once __DIR__ . '/../includes/config.php';

// --- Authentication Check ---
if (!is_logged_in() || !in_array(get_current_user()['role'], ['Headteacher', 'DOS', 'Teacher'])) {
    redirect('/index.php?route=login');
}

$page_title = "Analytics & AI Insights";
require_once APP_ROOT . '/includes/header.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Analytics & AI Insights</h1>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Student Performance Distribution</h6>
            </div>
            <div class="card-body">
                <canvas id="performancePieChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Attendance Trend (Last 7 Days)</h6>
            </div>
            <div class="card-body">
                <canvas id="attendanceLineChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
// Performance Pie Chart
var pieCtx = document.getElementById('performancePieChart').getContext('2d');
new Chart(pieCtx, {
    type: 'pie',
    data: {
        labels: ['Top Performers', 'Average', 'At-Risk'],
        datasets: [{
            data: [25, 150, 12],
            backgroundColor: ['#1cc88a', '#4e73df', '#f6c23e'],
        }]
    },
});

// Attendance Line Chart
var lineCtx = document.getElementById('attendanceLineChart').getContext('2d');
new Chart(lineCtx, {
    type: 'line',
    data: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [{
            label: 'Attendance %',
            data: [92, 95, 96, 94, 91, 88, 85],
            borderColor: '#4e73df',
            tension: 0.1
        }]
    },
});
</script>

<?php
require_once APP_ROOT . '/includes/footer.php';
?>

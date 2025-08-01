<?php
/**
 * EduPulse - Modern Student Summary Sheet
 *
 * A visually rich, one-page overview of a student's term performance.
 */

$page_title = "Student Summary Sheet";
check_permission(['DOS', 'Teacher', 'Student', 'Parent']);
$currentUser = get_current_user();
$school_id = $currentUser['school_id'];

// Placeholder data
$student_info = ['name' => 'Brian Sempala', 'class' => 'S.1', 'photo' => 'assets/images/placeholder.png'];
$key_stats = ['average' => 88, 'rank' => '3rd of 45', 'attendance' => '98%'];
$subject_scores = [
    'labels' => ['Math', 'English', 'Physics', 'Chemistry', 'History'],
    'student' => [85, 92, 81, 88, 90],
    'average' => [72, 78, 70, 75, 74]
];

require_once APP_ROOT . '/includes/header.php';
?>

<style>
    .summary-sheet { background: #fff; border-radius: 15px; padding: 2rem; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
    .student-photo { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid var(--primary-blue); }
    .stat-card { text-align: center; padding: 1rem; background: var(--light-blue); border-radius: 10px; }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Modern Summary Sheet</h1>
        <button class="btn btn-secondary"><i class="fas fa-file-pdf me-2"></i>Export as PDF</button>
    </div>

    <!-- Selection Form would go here -->

    <div class="summary-sheet">
        <div class="row">
            <!-- Left Column: Student Info & Stats -->
            <div class="col-lg-4 border-end">
                <div class="text-center p-3">
                    <img src="<?= BASE_URL . '/' . $student_info['photo'] ?>" alt="Student Photo" class="student-photo mb-3">
                    <h3><?= $student_info['name'] ?></h3>
                    <p class="text-muted fs-5"><?= $student_info['class'] ?></p>
                    <hr>
                    <div class="row g-2">
                        <div class="col-4"><div class="stat-card"><strong>Overall Avg.</strong><p class="fs-4 mb-0"><?= $key_stats['average'] ?>%</p></div></div>
                        <div class="col-4"><div class="stat-card"><strong>Class Rank</strong><p class="fs-4 mb-0"><?= $key_stats['rank'] ?></p></div></div>
                        <div class="col-4"><div class="stat-card"><strong>Attendance</strong><p class="fs-4 mb-0"><?= $key_stats['attendance'] ?></p></div></div>
                    </div>
                    <hr>
                    <h5>Teacher's Comment</h5>
                    <p class="text-muted fst-italic">"Brian has shown excellent progress this term, especially in Mathematics. He is a diligent and focused student. Keep up the great work!"</p>
                </div>
            </div>

            <!-- Right Column: Charts -->
            <div class="col-lg-8">
                <div class="p-3">
                    <h5 class="text-primary">Subject Performance Radar</h5>
                    <canvas id="radarChart" style="max-height: 300px;"></canvas>
                    <hr class="my-4">
                    <h5 class="text-primary">Comparison with Class Average</h5>
                    <canvas id="barChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Radar Chart
    new Chart($('#radarChart'), {
        type: 'radar',
        data: {
            labels: <?= json_encode($subject_scores['labels']) ?>,
            datasets: [{
                label: 'Your Score',
                data: <?= json_encode($subject_scores['student']) ?>,
                backgroundColor: 'rgba(0, 123, 255, 0.2)',
                borderColor: 'rgba(0, 123, 255, 1)',
                borderWidth: 2
            }]
        },
        options: { scales: { r: { beginAtZero: true, max: 100 } } }
    });

    // Bar Chart
    new Chart($('#barChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($subject_scores['labels']) ?>,
            datasets: [
                {
                    label: 'Your Score',
                    data: <?= json_encode($subject_scores['student']) ?>,
                    backgroundColor: 'rgba(0, 123, 255, 0.8)'
                },
                {
                    label: 'Class Average',
                    data: <?= json_encode($subject_scores['average']) ?>,
                    backgroundColor: 'rgba(108, 117, 125, 0.5)'
                }
            ]
        },
        options: { scales: { y: { beginAtZero: true, max: 100 } } }
    });
});
</script>

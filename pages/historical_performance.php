<?php
/**
 * EduPulse - Historical Performance Page
 *
 * Displays a student's academic performance over time using charts and tables.
 */

$page_title = "Student Historical Performance";
check_permission(['DOS', 'Teacher', 'Student', 'Parent']);
$currentUser = get_current_user();
$school_id = $currentUser['school_id'];

// Fetch classes for dropdown
try {
    $classes_stmt = $pdo->prepare("SELECT class_id, class_name FROM classes WHERE school_id = :school_id ORDER BY class_name");
    $classes_stmt->execute(['school_id' => $school_id]);
    $classes = $classes_stmt->fetchAll();
} catch (PDOException $e) {
    $classes = [];
    $error_message = "Error fetching classes.";
}

require_once APP_ROOT . '/includes/header.php';
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Historical Performance Analysis</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Select Student</h6>
        </div>
        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Class</label>
                    <select id="class_id_perf" class="form-select">
                        <option value="">Choose...</option>
                        <?php foreach ($classes as $class): ?>
                        <option value="<?= $class['class_id'] ?>"><?= sanitize($class['class_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Student</label>
                    <select id="student_id_perf" class="form-select">
                        <option value="">Select class first</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button id="loadPerfBtn" class="btn btn-info w-100">Load Data</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Chart and Table -->
    <div id="performanceDataContainer" style="display: none;">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Performance Trend Chart</h6>
            </div>
            <div class="card-body">
                <canvas id="performanceChart"></canvas>
            </div>
        </div>
        <div class="card shadow mb-4">
             <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Detailed Marks</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr><th>Term</th><th>Year</th><th>Subject</th><th>Assessment</th><th>Score</th></tr>
                        </thead>
                        <tbody id="marksTableBody">
                            <!-- Data will be populated here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/includes/footer.php'; ?>

<script>
$(document).ready(function() {
    let performanceChart = null;

    // AJAX for student dropdown
    $('#class_id_perf').on('change', function() {
        const classId = $(this).val();
        const studentDropdown = $('#student_id_perf');
        studentDropdown.html('<option value="">Loading...</option>');

        if (!classId) {
            studentDropdown.html('<option value="">Select a class first</option>');
            return;
        }

        $.ajax({
            url: '<?= BASE_URL ?>/api/get_students_by_class.php',
            type: 'GET',
            data: { class_id: classId },
            dataType: 'json',
            success: function(response) {
                let options = '<option value="">Select a student...</option>';
                if (response.status === 'success' && response.students.length > 0) {
                    response.students.forEach(student => {
                        options += `<option value="${student.user_id}">${student.first_name} ${student.last_name}</option>`;
                    });
                } else {
                    options = '<option value="">No students found</option>';
                }
                studentDropdown.html(options);
            },
            error: function() {
                studentDropdown.html('<option value="">Error loading students</option>');
            }
        });
    });

    $('#loadPerfBtn').on('click', function() {
        // Placeholder for loading performance data via AJAX
        const studentId = $('#student_id_perf').val();
        if(!studentId) {
            alert('Please select a student.');
            return;
        }

        $('#performanceDataContainer').show();

        // Dummy data representing an AJAX response
        const performanceData = {
            labels: ['Term 1 2024', 'Term 2 2024', 'Term 3 2024', 'Term 1 2025'],
            datasets: [{
                label: 'Mathematics',
                data: [65, 70, 75, 85],
                borderColor: 'blue',
                tension: 0.1
            }, {
                label: 'English',
                data: [80, 78, 82, 85],
                borderColor: 'green',
                tension: 0.1
            }]
        };

        if (performanceChart) {
            performanceChart.destroy();
        }

        performanceChart = new Chart($('#performanceChart'), {
            type: 'line',
            data: performanceData,
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    title: { display: true, text: 'Performance Over Time' }
                },
                scales: { y: { beginAtZero: true, max: 100 } }
            }
        });

        // Dummy data for the table
        $('#marksTableBody').html(`
            <tr><td>1</td><td>2025</td><td>Mathematics</td><td>Mid-Term</td><td>85</td></tr>
            <tr><td>3</td><td>2024</td><td>English</td><td>Final Exam</td><td>82</td></tr>
        `);
    });
});
</script>

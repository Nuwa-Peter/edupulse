<?php
/**
 * EduPulse - Attendance Management Page
 *
 * Allows teachers to mark daily attendance and administrators to view reports.
 */

$page_title = "Attendance";
check_permission(['DOS', 'Teacher']);
$currentUser = get_current_user();
$school_id = $currentUser['school_id'];

// Fetch classes for dropdowns
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
    <h1 class="h3 mb-4 text-gray-800">Attendance Management</h1>

    <div class="card shadow">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#markAttendance">Mark Attendance</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#attendanceReports">Reports</a>
                </li>
                 <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#qrSystem">QR Code System</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <!-- Mark Attendance Tab -->
                <div class="tab-pane fade show active" id="markAttendance">
                    <h5 class="card-title">Mark Daily Attendance</h5>
                    <div class="row align-items-end mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Select Class</label>
                            <select id="class_id_attendance" class="form-select">
                                <option value="">Choose...</option>
                                <?php foreach ($classes as $class): ?>
                                <option value="<?= $class['class_id'] ?>"><?= sanitize($class['class_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date</label>
                            <input type="date" id="attendance_date" class="form-control" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-2">
                             <button id="loadStudentListBtn" class="btn btn-primary w-100">Load List</button>
                        </div>
                    </div>
                    <form method="POST" action="">
                        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr><th>Student Name</th><th>Status</th></tr>
                                </thead>
                                <tbody id="attendanceTbody">
                                    <tr><td colspan="2" class="text-muted text-center">Select a class to load students.</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <button type="submit" name="save_attendance" class="btn btn-success">Save Attendance</button>
                    </form>
                </div>

                <!-- Reports Tab -->
                <div class="tab-pane fade" id="attendanceReports">
                     <h5 class="card-title">Attendance Reports</h5>
                     <p>Filter and view attendance records. (Functionality to be implemented)</p>
                </div>

                <!-- QR Code System Tab -->
                <div class="tab-pane fade" id="qrSystem">
                     <h5 class="card-title">QR Code Attendance System</h5>
                     <p>This system allows for quick, scannable attendance.</p>
                     <h6>Workflow:</h6>
                     <ol>
                        <li>Administrator generates a unique QR code for a class session.</li>
                        <li>The QR code is displayed to students.</li>
                        <li>Students scan the code with their device (future mobile app or web portal).</li>
                        <li>The teacher's attendance list shows students who have scanned, pending verification.</li>
                        <li>Teacher confirms the students are physically present and saves the record.</li>
                     </ol>
                     <button class="btn btn-info">Generate Today's QR Code for Class...</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/includes/footer.php'; ?>

<script>
$(document).ready(function() {
    $('#loadStudentListBtn').on('click', function() {
        // Placeholder for loading student list via AJAX
        const tbody = $('#attendanceTbody');
        tbody.html(''); // Clear
        const students = [
            { id: 1, name: 'Brian Sempala' },
            { id: 2, name: 'Jane Doe' }
        ];

        students.forEach(student => {
            const row = `
                <tr>
                    <td>${student.name}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="status[${student.id}]" id="present_${student.id}" value="Present" autocomplete="off" checked>
                            <label class="btn btn-outline-success" for="present_${student.id}">Present</label>

                            <input type="radio" class="btn-check" name="status[${student.id}]" id="absent_${student.id}" value="Absent" autocomplete="off">
                            <label class="btn btn-outline-danger" for="absent_${student.id}">Absent</label>

                            <input type="radio" class="btn-check" name="status[${student.id}]" id="late_${student.id}" value="Late" autocomplete="off">
                            <label class="btn btn-outline-warning" for="late_${student.id}">Late</label>
                        </div>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
    });
});
</script>

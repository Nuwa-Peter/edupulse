<?php
/**
 * EduPulse - Marks Entry Page
 *
 * Allows Teachers and DOS to enter student marks for specific assessments.
 */

$page_title = "Enter Marks";
check_permission(['DOS', 'Teacher']);
$currentUser = get_current_user();
$school_id = $currentUser['school_id'];

$error_message = '';
$success_message = '';

// Handle form submission for saving marks
if (isset($_POST['save_marks'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error_message = "Invalid request.";
    } else {
        // Complex logic for looping through student marks and UPSERTing into the database
        $success_message = "Marks saved successfully (Placeholder).";
    }
}

// Fetch data for dropdowns
try {
    $classes_stmt = $pdo->prepare("SELECT class_id, class_name FROM classes WHERE school_id = :school_id ORDER BY class_name");
    $classes_stmt->execute(['school_id' => $school_id]);
    $classes = $classes_stmt->fetchAll();

    $subjects_stmt = $pdo->query("SELECT subject_id, subject_name FROM subjects ORDER BY subject_name");
    $subjects = $subjects_stmt->fetchAll();
} catch (PDOException $e) {
    $classes = [];
    $subjects = [];
    $error_message = "Error fetching initial data.";
}

require_once APP_ROOT . '/includes/header.php';
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Enter Student Marks</h1>

    <?php if ($error_message) echo "<div class='alert alert-danger'>$error_message</div>"; ?>
    <?php if ($success_message) echo "<div class='alert alert-success'>$success_message</div>"; ?>

    <!-- Selection Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Select Criteria</h6>
        </div>
        <div class="card-body">
            <form id="marksSelectionForm">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="class_id" class="form-label">Class</label>
                        <select id="class_id" name="class_id" class="form-select" required>
                            <option value="">Choose...</option>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?= $class['class_id'] ?>"><?= sanitize($class['class_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="subject_id" class="form-label">Subject</label>
                        <select id="subject_id" name="subject_id" class="form-select" required>
                            <option value="">Choose...</option>
                            <?php foreach ($subjects as $subject): ?>
                                <option value="<?= $subject['subject_id'] ?>"><?= sanitize($subject['subject_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="term" class="form-label">Term</label>
                        <select id="term" name="term" class="form-select" required>
                            <option value="1">Term 1</option>
                            <option value="2">Term 2</option>
                            <option value="3">Term 3</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="assessment_type" class="form-label">Assessment</label>
                        <input type="text" id="assessment_type" name="assessment_type" class="form-control" placeholder="e.g., Mid-Term" required>
                    </div>
                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <button type="button" id="loadStudentsBtn" class="btn btn-info w-100">Load Students</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Marks Entry Table (initially hidden) -->
    <div id="marksEntryContainer" class="card shadow mb-4" style="display: none;">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Enter Marks for <span id="marksHeaderDetails"></span></h6>
        </div>
        <div class="card-body">
            <form id="marksEntryForm">
                <div id="form-messages" class="mb-3"></div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Student ID</th>
                                <th style="width: 15%;">Mark (/100)</th>
                            </tr>
                        </thead>
                        <tbody id="studentMarksTbody">
                            <!-- Student rows will be loaded here via AJAX -->
                        </tbody>
                    </table>
                </div>
                <button type="submit" class="btn btn-success">Save All Marks</button>
            </form>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/includes/footer.php'; ?>

<script>
$(document).ready(function() {
    $('#loadStudentsBtn').on('click', function() {
        const classId = $('#class_id').val();
        const className = $('#class_id option:selected').text();
        const subjectName = $('#subject_id option:selected').text();

        if (!classId) {
            alert('Please select a class.');
            return;
        }

        // Show the container and update header
        $('#marksEntryContainer').show();
        $('#marksHeaderDetails').text(`${className} - ${subjectName}`);

        // Populate hidden form fields
        $('#form_class_id').val(classId);
        $('#form_subject_id').val($('#subject_id').val());
        $('#form_term').val($('#term').val());
        $('#form_assessment_type').val($('#assessment_type').val());

        // AJAX call to get students for the class
        // In a real app, this would be a separate PHP file e.g., /api/get_students.php
        // For this placeholder, we'll just add some dummy data.
        const studentMarksTbody = $('#studentMarksTbody');
        studentMarksTbody.html('<tr><td colspan="3" class="text-center">Loading...</td></tr>');

        const studentMarksTbody = $('#studentMarksTbody');
        studentMarksTbody.html('<tr><td colspan="3" class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>');

        $.ajax({
            url: '<?= BASE_URL ?>/api/get_students_by_class.php',
            type: 'GET',
            data: { class_id: classId },
            dataType: 'json',
            success: function(response) {
                let rows = '';
                if (response.status === 'success' && response.students.length > 0) {
                    response.students.forEach(student => {
                        rows += `<tr>
                                    <td>${student.first_name} ${student.last_name}</td>
                                    <td>${student.edupulse_id}</td>
                                    <td>
                                        <input type="number" name="marks[${student.user_id}]" class="form-control" min="0" max="100">
                                    </td>
                                 </tr>`;
                    });
                } else {
                    rows = '<tr><td colspan="3" class="text-center">No students found in this class.</td></tr>';
                }
                studentMarksTbody.html(rows);
            },
            error: function() {
                studentMarksTbody.html('<tr><td colspan="3" class="text-center text-danger">Failed to load students.</td></tr>');
            }
        });
    });

    // Handle AJAX form submission
    $('#marksEntryForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        const selectionData = $('#marksSelectionForm').serialize();
        const fullData = formData + '&' + selectionData;
        const messagesDiv = $('#form-messages');

        messagesDiv.html('<div class="alert alert-info">Saving...</div>');

        $.ajax({
            url: '<?= BASE_URL ?>/api/save_marks.php',
            type: 'POST',
            data: fullData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    messagesDiv.html(`<div class="alert alert-success">${response.message}</div>`);
                } else {
                    messagesDiv.html(`<div class="alert alert-danger">${response.message}</div>`);
                }
            },
            error: function() {
                 messagesDiv.html('<div class="alert alert-danger">An error occurred while submitting the form.</div>');
            }
        });
    });
});
</script>

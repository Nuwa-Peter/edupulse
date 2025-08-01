<?php
/**
 * EduPulse - Report Card Generation Page
 *
 * Allows authorized users to generate and download student report cards as PDFs.
 */

$page_title = "Generate Report Cards";
check_permission(['DOS', 'Teacher', 'Student', 'Parent']);
$currentUser = get_current_user();
$school_id = $currentUser['school_id'];

// Handle PDF generation request
if (isset($_POST['generate_report'])) {
    // This is where the complex PDF generation logic would go.
    // It would use the TCPDF library.
    // 1. Get student, class, term, year from POST.
    // 2. Fetch all required data from the database.
    // 3. Construct an HTML template for the report card.
    // 4. Use TCPDF to convert HTML to PDF.
    // 5. Force download the PDF.

    // For now, we'll just show a placeholder message.
    $success_message = "Report card generation initiated (Placeholder).";
}


// Fetch data for dropdowns
try {
    $classes_stmt = $pdo->prepare("SELECT class_id, class_name FROM classes WHERE school_id = :school_id ORDER BY class_name");
    $classes_stmt->execute(['school_id' => $school_id]);
    $classes = $classes_stmt->fetchAll();
} catch (PDOException $e) {
    $classes = [];
    $error_message = "Error fetching initial data.";
}

require_once APP_ROOT . '/includes/header.php';
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Generate Report Cards</h1>

    <?php if (isset($error_message)) echo "<div class='alert alert-danger'>$error_message</div>"; ?>
    <?php if (isset($success_message)) echo "<div class='alert alert-success'>$success_message</div>"; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Select Report Criteria</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                <div class="row align-items-end">
                    <div class="col-md-3 mb-3">
                        <label for="class_id" class="form-label">Class</label>
                        <select id="class_id" name="class_id" class="form-select" required>
                            <option value="">Choose a class...</option>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?= $class['class_id'] ?>"><?= sanitize($class['class_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="student_id" class="form-label">Student</label>
                        <select id="student_id" name="student_id" class="form-select" required>
                            <option value="">Select a class first</option>
                            <!-- Students will be loaded here via AJAX -->
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
                    <div class="col-md-1 mb-3">
                         <label for="year" class="form-label">Year</label>
                        <input type="number" id="year" name="year" class="form-control" value="<?= date('Y') ?>" required>
                    </div>
                    <div class="col-md-2 mb-3">
                        <button type="submit" name="generate_report" class="btn btn-primary w-100">Generate PDF</button>
                    </div>
                </div>
                 <div class="mt-2">
                     <button type="submit" name="generate_bulk_report" class="btn btn-secondary">Generate for Entire Class</button>
                 </div>
            </form>
        </div>
    </div>

    <!-- Report Card Preview Area -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Report Preview</h6>
        </div>
        <div class="card-body">
            <p class="text-center text-muted">The generated report card PDF will be available for download here.</p>
            <!-- An iframe could be used to display the PDF preview -->
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/includes/footer.php'; ?>

<script>
$(document).ready(function() {
    $('#class_id').on('change', function() {
        const classId = $(this).val();
        const studentDropdown = $('#student_id');
        studentDropdown.html('<option value="">Loading...</option>');

        if (!classId) {
            studentDropdown.html('<option value="">Select a class first</option>');
            return;
        }

        // AJAX call to get students for the class
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
});
</script>

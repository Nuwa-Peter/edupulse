<?php
/**
 * EduPulse - Student ID Card Generation Page
 *
 * Allows authorized users to generate and download student ID cards as PDFs.
 */

$page_title = "Generate Student ID Cards";
check_permission(['DOS', 'Student']);
$currentUser = get_current_user();
$school_id = $currentUser['school_id'];

// Handle PDF generation request
if (isset($_POST['generate_id'])) {
    // This is where the complex PDF generation logic for ID cards would go.
    // It would use the TCPDF library with a custom page size (85.6mm x 54mm).
    // 1. Get student, class from POST.
    // 2. Fetch student data (name, photo, edupulse_id).
    // 3. Construct an HTML template for the ID card.
    // 4. Use TCPDF to convert HTML to PDF.
    // 5. Force download the PDF.

    $success_message = "ID Card generation initiated (Placeholder).";
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
    <h1 class="h3 mb-4 text-gray-800">Generate Student ID Cards</h1>

    <?php if (isset($error_message)) echo "<div class='alert alert-danger'>$error_message</div>"; ?>
    <?php if (isset($success_message)) echo "<div class='alert alert-success'>$success_message</div>"; ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Select Student</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                        <div class="row align-items-end">
                            <div class="col-md-5 mb-3">
                                <label for="class_id" class="form-label">Class</label>
                                <select id="class_id" name="class_id" class="form-select" required>
                                    <option value="">Choose a class...</option>
                                    <?php foreach ($classes as $class): ?>
                                        <option value="<?= $class['class_id'] ?>"><?= sanitize($class['class_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-5 mb-3">
                                <label for="student_id" class="form-label">Student</label>
                                <select id="student_id" name="student_id" class="form-select" required>
                                    <option value="">Select a class first</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <button type="submit" name="generate_id" class="btn btn-primary w-100">Generate</button>
                            </div>
                        </div>
                         <div class="mt-2">
                             <button type="submit" name="generate_bulk_ids" class="btn btn-secondary">Generate for Entire Class</button>
                         </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
             <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">ID Card Preview</h6>
                </div>
                <div class="card-body text-center">
                    <p class="text-muted">A preview of the generated CR80 ID card will appear here.</p>
                    <!-- Placeholder image of an ID card -->
                    <img src="https://via.placeholder.com/250x150.png?text=ID+Card+Front" alt="ID Card Preview" class="img-fluid">
                </div>
            </div>
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

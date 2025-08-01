<?php
/**
 * EduPulse - Parent Feedback Surveys Page
 *
 * Allows Headteachers to create surveys and Parents to respond.
 */

$page_title = "Feedback Surveys";
check_permission(['Headteacher', 'Parent']);
$currentUser = get_current_user();
$school_id = $currentUser['school_id'];
$userRole = $currentUser['role'];

// Fetch surveys
try {
    if ($userRole === 'Headteacher') {
        // HT sees all surveys
        $stmt = $pdo->prepare("SELECT * FROM surveys WHERE school_id = :school_id ORDER BY created_at DESC");
        $stmt->execute(['school_id' => $school_id]);
    } else {
        // Parent sees active, uncompleted surveys
        // This is a complex query, placeholder for now
        $stmt = $pdo->prepare("SELECT * FROM surveys WHERE school_id = :school_id AND end_date > NOW() ORDER BY created_at DESC");
        $stmt->execute(['school_id' => $school_id]);
    }
    $surveys = $stmt->fetchAll();
} catch (PDOException $e) {
    $surveys = [];
    $error_message = "Error fetching surveys.";
}

require_once APP_ROOT . '/includes/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Parent Feedback Surveys</h1>
        <?php if ($userRole === 'Headteacher'): ?>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSurveyModal"><i class="fas fa-plus me-2"></i>Create New Survey</button>
        <?php endif; ?>
    </div>

    <!-- Headteacher View -->
    <?php if ($userRole === 'Headteacher'): ?>
    <div class="card shadow">
        <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">Created Surveys</h6></div>
        <div class="card-body">
            <ul class="list-group">
                <?php foreach($surveys as $survey): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong><?= sanitize($survey['title']) ?></strong>
                        <p class="mb-0 text-muted small">Runs from <?= date('M d', strtotime($survey['start_date'])) ?> to <?= date('M d, Y', strtotime($survey['end_date'])) ?></p>
                    </div>
                    <a href="#" class="btn btn-info btn-sm">View Results</a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>

    <!-- Parent View -->
    <?php if ($userRole === 'Parent'): ?>
    <div class="card shadow">
        <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">Available Surveys</h6></div>
        <div class="card-body">
             <ul class="list-group">
                <?php foreach($surveys as $survey): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong><?= sanitize($survey['title']) ?></strong>
                        <p class="mb-0 text-muted small"><?= sanitize($survey['description']) ?></p>
                    </div>
                    <a href="#" class="btn btn-success btn-sm">Take Survey</a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>

</div>

<!-- Create Survey Modal (for Headteacher) -->
<?php if ($userRole === 'Headteacher'): ?>
<div class="modal fade" id="createSurveyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Create New Feedback Survey</h5></div>
            <div class="modal-body">
                <form>
                    <div class="mb-3"><label class="form-label">Survey Title</label><input type="text" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Description</label><textarea class="form-control" rows="2"></textarea></div>
                    <hr>
                    <h6>Questions</h6>
                    <div id="questions-container">
                        <!-- Questions will be added dynamically here -->
                        <div class="question-item mb-2"><input type="text" class="form-control" placeholder="Question 1"></div>
                    </div>
                    <button type="button" id="addQuestionBtn" class="btn btn-sm btn-secondary">Add Question</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Launch Survey</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>


<?php require_once APP_ROOT . '/includes/footer.php'; ?>

<script>
$(document).ready(function() {
    $('#addQuestionBtn').on('click', function() {
        const questionNum = $('#questions-container').children().length + 1;
        $('#questions-container').append(`<div class="question-item mb-2"><input type="text" class="form-control" placeholder="Question ${questionNum}"></div>`);
    });
});
</script>

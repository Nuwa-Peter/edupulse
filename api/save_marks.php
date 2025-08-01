<?php
/**
 * EduPulse - API Endpoint for saving student marks
 *
 * Receives marks data from an AJAX form submission and updates the database.
 */

require_once dirname(__DIR__) . '/includes/config.php';

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

// Security checks
if (!is_logged_in() || !in_array(get_current_user()['role'], ['DOS', 'Teacher'])) {
    $response['message'] = 'Unauthorized';
    echo json_encode($response);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method.';
    echo json_encode($response);
    exit();
}

// In a real app, you would verify the CSRF token sent via AJAX headers
// if (!verify_csrf_token($_POST['csrf_token'])) { ... }

// Get data
$marks = $_POST['marks'] ?? [];
$class_id = filter_input(INPUT_POST, 'class_id', FILTER_VALIDATE_INT);
$subject_id = filter_input(INPUT_POST, 'subject_id', FILTER_VALIDATE_INT);
$term = filter_input(INPUT_POST, 'term', FILTER_SANITIZE_STRING);
$assessment_type = filter_input(INPUT_POST, 'assessment_type', FILTER_SANITIZE_STRING);
$academic_year = date('Y'); // Assuming current year
$recorded_by_id = $_SESSION['user_id'];

if (empty($marks) || !$class_id || !$subject_id || !$term || !$assessment_type) {
    $response['message'] = 'Missing required data.';
    echo json_encode($response);
    exit();
}

// Database UPSERT logic
try {
    $pdo->beginTransaction();

    $sql = "INSERT INTO marks (student_id, subject_id, term, academic_year, assessment_type, score, recorded_by_id)
            VALUES (:student_id, :subject_id, :term, :academic_year, :assessment_type, :score, :recorded_by_id)
            ON DUPLICATE KEY UPDATE score = VALUES(score)";

    $stmt = $pdo->prepare($sql);

    foreach ($marks as $student_id => $score) {
        // Skip empty scores
        if ($score === '' || $score === null) {
            continue;
        }

        $stmt->execute([
            'student_id' => $student_id,
            'subject_id' => $subject_id,
            'term' => $term,
            'academic_year' => $academic_year,
            'assessment_type' => $assessment_type,
            'score' => $score,
            'recorded_by_id' => $recorded_by_id
        ]);
    }

    $pdo->commit();
    $response['status'] = 'success';
    $response['message'] = 'Marks have been saved successfully!';

} catch (PDOException $e) {
    $pdo->rollBack();
    $response['message'] = 'Database error: ' . $e->getMessage();
}

echo json_encode($response);
?>

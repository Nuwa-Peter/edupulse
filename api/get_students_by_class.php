<?php
/**
 * EduPulse - API Endpoint for fetching students by class
 *
 * Returns a JSON list of students for a given class_id.
 */

require_once dirname(__DIR__) . '/includes/config.php';

header('Content-Type: application/json');

// Basic security: check if user is logged in
if (!is_logged_in()) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$class_id = filter_input(INPUT_GET, 'class_id', FILTER_VALIDATE_INT);

if (!$class_id) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid class ID.']);
    exit();
}

try {
    $stmt = $pdo->prepare(
        "SELECT u.user_id, u.first_name, u.last_name, u.edupulse_id
         FROM users u
         JOIN students s ON u.user_id = s.user_id
         WHERE s.class_id = :class_id
         ORDER BY u.last_name, u.first_name"
    );
    $stmt->execute(['class_id' => $class_id]);
    $students = $stmt->fetchAll();

    echo json_encode(['status' => 'success', 'students' => $students]);

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database query failed.']);
}
?>

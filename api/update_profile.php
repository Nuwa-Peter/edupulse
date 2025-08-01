<?php
/**
 * EduPulse - API Endpoint for updating user profile info
 */

require_once dirname(__DIR__) . '/includes/config.php';

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

if (!is_logged_in()) {
    $response['message'] = 'Unauthorized';
    echo json_encode($response);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method.';
    echo json_encode($response);
    exit();
}

$user_id = $_SESSION['user_id'];
$first_name = sanitize($_POST['first_name'] ?? '');
$last_name = sanitize($_POST['last_name'] ?? '');
$phone = sanitize($_POST['phone'] ?? '');

if (empty($first_name) || empty($last_name)) {
    $response['message'] = 'First and last name are required.';
    echo json_encode($response);
    exit();
}

try {
    $stmt = $pdo->prepare("UPDATE users SET first_name = :first_name, last_name = :last_name, phone = :phone WHERE user_id = :user_id");
    $stmt->execute([
        'first_name' => $first_name,
        'last_name' => $last_name,
        'phone' => $phone,
        'user_id' => $user_id
    ]);

    // Update session data
    $_SESSION['user']['first_name'] = $first_name;

    $response['status'] = 'success';
    $response['message'] = 'Profile updated successfully!';

} catch (PDOException $e) {
    $response['message'] = 'Database error: ' . $e->getMessage();
}

echo json_encode($response);
?>

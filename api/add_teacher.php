<?php
/**
 * EduPulse - API Endpoint for adding a new teacher
 */

require_once dirname(__DIR__) . '/includes/config.php';

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

// Security checks
check_permission(['Headteacher']);
$currentUser = get_current_user();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method.';
    echo json_encode($response);
    exit();
}

// Get POST data
$first_name = sanitize($_POST['first_name'] ?? '');
$last_name = sanitize($_POST['last_name'] ?? '');
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$phone = sanitize($_POST['phone'] ?? '');
$school_id = $currentUser['school_id'];

if (empty($first_name) || empty($last_name) || empty($email)) {
    $response['message'] = 'First name, last name, and email are required.';
    echo json_encode($response);
    exit();
}

try {
    // Check if email is already in use
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        $response['message'] = 'This email address is already registered.';
        echo json_encode($response);
        exit();
    }

    $pdo->beginTransaction();

    // Generate initial password and edupulse ID
    $initial_password = bin2hex(random_bytes(4)); // 8-char random password
    $password_hash = password_hash($initial_password, PASSWORD_DEFAULT);
    $edupulse_id = 'T-' . $school_id . '-' . rand(1000, 9999);

    $user_stmt = $pdo->prepare(
        "INSERT INTO users (edupulse_id, school_id, first_name, last_name, email, phone, password_hash, role, status)
         VALUES (:edupulse_id, :school_id, :first_name, :last_name, :email, :phone, :password_hash, 'Teacher', 'active')"
    );
    $user_stmt->execute([
        'edupulse_id' => $edupulse_id,
        'school_id' => $school_id,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email,
        'phone' => $phone,
        'password_hash' => $password_hash
    ]);

    $pdo->commit();

    // Send welcome email with initial password (placeholder logic)
    // mail($email, 'Welcome to EduPulse', 'Your account has been created. Your password is: ' . $initial_password);

    $response['status'] = 'success';
    $response['message'] = "Teacher '$first_name $last_name' created successfully. Their initial password is: $initial_password (this would be emailed in a real app).";

} catch (PDOException $e) {
    $pdo->rollBack();
    $response['message'] = 'Database error: ' . $e->getMessage();
}

echo json_encode($response);
?>

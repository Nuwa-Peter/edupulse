<?php
/**
 * EduPulse - API Endpoint for sending a chat message
 */

require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

// Security & Auth
if (!is_logged_in() || !in_array(get_current_user()['role'], ['Headteacher', 'Teacher'])) {
    $response['message'] = 'Unauthorized';
    echo json_encode($response);
    exit();
}

// Get data
$sender_id = $_SESSION['user_id'];
$receiver_id = filter_input(INPUT_POST, 'receiver_id', FILTER_VALIDATE_INT);
$message = sanitize($_POST['message'] ?? '');

if (empty($receiver_id) || empty($message)) {
    $response['message'] = 'Missing receiver or message content.';
    echo json_encode($response);
    exit();
}

// --- Save to Database & Trigger Pusher ---
try {
    // 1. Encrypt the message
    $encrypted_message = encrypt_data($message);
    if ($encrypted_message === false) {
        throw new Exception('Encryption failed.');
    }

    // 2. Save to database
    $stmt = $pdo->prepare(
        "INSERT INTO chat_messages (sender_id, receiver_id, message_content, is_read) VALUES (?, ?, ?, 0)"
    );
    $stmt->execute([$sender_id, $receiver_id, $encrypted_message]);
    $message_id = $pdo->lastInsertId();

    // 3. Trigger Pusher event
    $pusher = new Pusher\Pusher(PUSHER_APP_KEY, PUSHER_APP_SECRET, PUSHER_APP_ID, ['cluster' => PUSHER_CLUSTER]);

    // Channel name must be consistent
    $user1 = min($sender_id, $receiver_id);
    $user2 = max($sender_id, $receiver_id);
    $channel_name = "private-chat-{$user1}-{$user2}";

    $data_to_push = [
        'message_id' => $message_id,
        'sender_id' => $sender_id,
        'message' => $message, // Send the unencrypted message
        'timestamp' => date('Y-m-d H:i:s')
    ];
    $pusher->trigger($channel_name, 'new-message', $data_to_push);

    $response['status'] = 'success';
    $response['message'] = 'Message sent.';

} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
?>

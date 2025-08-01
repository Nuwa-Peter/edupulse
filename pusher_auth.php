<?php
/**
 * EduPulse - Pusher Authentication Endpoint
 *
 * This script authenticates a logged-in user's subscription to a private channel.
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/vendor/autoload.php';

if (!is_logged_in()) {
    header('', true, 403);
    echo "Forbidden";
    exit();
}

$pusher = new Pusher\Pusher(
    PUSHER_APP_KEY,
    PUSHER_APP_SECRET,
    PUSHER_APP_ID,
    ['cluster' => PUSHER_CLUSTER]
);

// The channel name is expected in the format: private-chat-{user1_id}-{user2_id}
$channel_name = $_POST['channel_name'];
$socket_id = $_POST['socket_id'];
$current_user_id = $_SESSION['user_id'];

// --- Authorization Logic ---
// Extract user IDs from the channel name
preg_match('/private-chat-(\d+)-(\d+)/', $channel_name, $matches);

if (count($matches) !== 3) {
    header('', true, 400);
    echo "Invalid channel name";
    exit();
}

$user_id_1 = (int)$matches[1];
$user_id_2 = (int)$matches[2];

// Check if the currently logged-in user is one of the two in the channel name
if ($current_user_id === $user_id_1 || $current_user_id === $user_id_2) {
    // If authorized, echo the auth string
    echo $pusher->authorizeChannel($channel_name, $socket_id);
} else {
    // If not authorized, return a 403 Forbidden status
    header('', true, 403);
    echo "Forbidden: You are not authorized to access this channel.";
}
?>

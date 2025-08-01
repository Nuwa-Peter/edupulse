<?php
/**
 * EduPulse - Real-time Chat Page
 *
 * Facilitates real-time, encrypted chat between Headteachers and Teachers.
 */

$page_title = "Chat";
check_permission(['Headteacher', 'Teacher']);
$currentUser = get_current_user();
$school_id = $currentUser['school_id'];
$userRole = $currentUser['role'];
$user_id = $_SESSION['user_id'];

// Fetch contacts
try {
    if ($userRole === 'Headteacher') {
        // HT sees all teachers
        $stmt = $pdo->prepare("SELECT user_id, first_name, last_name, profile_photo_url FROM users WHERE school_id = :school_id AND role = 'Teacher'");
        $stmt->execute(['school_id' => $school_id]);
    } else {
        // Teacher sees the HT
        $stmt = $pdo->prepare("SELECT user_id, first_name, last_name, profile_photo_url FROM users WHERE school_id = :school_id AND role = 'Headteacher'");
        $stmt->execute(['school_id' => $school_id]);
    }
    $contacts = $stmt->fetchAll();
} catch (PDOException $e) {
    $contacts = [];
    $error_message = "Error fetching contacts.";
}

require_once APP_ROOT . '/includes/header.php';
?>

<style>
    /* Page-specific styles for chat interface */
    .chat-app { display: flex; height: 75vh; }
    .chat-contacts { width: 300px; border-right: 1px solid #dee2e6; overflow-y: auto; }
    .chat-window { flex-grow: 1; display: flex; flex-direction: column; }
    .chat-messages { flex-grow: 1; padding: 20px; overflow-y: auto; }
    .chat-footer { padding: 10px; border-top: 1px solid #dee2e6; }
    .message { margin-bottom: 15px; }
    .message .bubble { padding: 10px 15px; border-radius: 20px; max-width: 70%; }
    .message.sent .bubble { background-color: var(--primary-blue); color: white; margin-left: auto; }
    .message.received .bubble { background-color: #e9ecef; }
</style>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Chat</h1>

    <div class="card shadow">
        <div class="card-body p-0">
            <div class="chat-app">
                <!-- Contacts List -->
                <div class="chat-contacts">
                    <div class="p-3 fw-bold border-bottom">Contacts</div>
                    <ul class="list-group list-group-flush">
                        <?php foreach($contacts as $contact): ?>
                        <li class="list-group-item list-group-item-action" data-contact-id="<?= $contact['user_id'] ?>">
                            <img src="<?= BASE_URL . '/' . sanitize($contact['profile_photo_url']) ?>" class="rounded-circle me-2" width="40" height="40">
                            <?= sanitize($contact['first_name'] . ' ' . $contact['last_name']) ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <!-- Chat Window -->
                <div class="chat-window">
                    <div class="chat-header p-3 border-bottom fw-bold" id="chat-header">
                        Select a contact to start chatting
                    </div>
                    <div class="chat-messages" id="chat-messages">
                        <!-- Messages will be loaded here -->
                    </div>
                    <div class="chat-footer">
                        <div class="input-group">
                            <input type="text" id="message-input" class="form-control" placeholder="Type a message...">
                            <button id="send-btn" class="btn btn-primary">Send</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/includes/footer.php'; ?>

<script>
const PUSHER_APP_KEY = '<?= PUSHER_APP_KEY ?>';
const PUSHER_CLUSTER = '<?= PUSHER_CLUSTER ?>';
const currentUserId = <?= $user_id ?>;
let activeContactId = null;
let currentChannel = null;

const pusher = new Pusher(PUSHER_APP_KEY, {
    cluster: PUSHER_CLUSTER,
    authEndpoint: '<?= BASE_URL ?>/pusher_auth.php'
});

function appendMessage(text, type) {
    const messageHtml = `<div class="message ${type}"><div class="bubble">${text}</div></div>`;
    const chatMessages = $('#chat-messages');
    chatMessages.append(messageHtml);
    chatMessages.scrollTop(chatMessages[0].scrollHeight);
}

function selectContact(contactElement) {
    // Unsubscribe from old channel if it exists
    if (currentChannel) {
        pusher.unsubscribe(currentChannel.name);
    }

    $('.chat-contacts .list-group-item').removeClass('active');
    contactElement.addClass('active');

    activeContactId = contactElement.data('contact-id');
    const contactName = contactElement.text().trim();
    $('#chat-header').text(`Chat with ${contactName}`);
    $('#chat-messages').html('<p class="text-center text-muted">Loading...</p>');

    // In a real app, you would load history via AJAX.
    // For now, we clear it.
    setTimeout(() => $('#chat-messages').html(''), 200);
    $('#message-input').prop('disabled', false);

    // Subscribe to the new private channel
    const user1 = Math.min(currentUserId, activeContactId);
    const user2 = Math.max(currentUserId, activeContactId);
    const channelName = `private-chat-${user1}-${user2}`;

    currentChannel = pusher.subscribe(channelName);
    currentChannel.bind('new-message', function(data) {
        // Only display if the message is from the active contact
        if (data.sender_id == activeContactId) {
            appendMessage(data.message, 'received');
        }
    });
}

function sendMessage() {
    const messageText = $('#message-input').val();
    if (!messageText || !activeContactId) return;

    appendMessage(messageText, 'sent');

    $.post('<?= BASE_URL ?>/api/send_message.php', {
        receiver_id: activeContactId,
        message: messageText
    }).fail(function() {
        appendMessage('Failed to send message.', 'error');
    });

    $('#message-input').val('');
}

// Event Listeners
$('.chat-contacts .list-group-item').on('click', function() {
    selectContact($(this));
});

$('#send-btn').on('click', sendMessage);
$('#message-input').on('keypress', function(e) {
    if (e.which === 13) sendMessage();
});

// Initially disable input
$('#message-input').prop('disabled', true);

</script>

<?php
/**
 * EduPulse - Chat
 */

require_once __DIR__ . '/../includes/config.php';

// --- Authentication Check ---
if (!is_logged_in() || !in_array(get_current_user()['role'], ['Headteacher', 'Teacher'])) {
    redirect('/index.php?route=login');
}

$page_title = "Chat";
require_once APP_ROOT . '/includes/header.php';
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Real-time Chat</h1>

    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    Contacts
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">Jane Smith</li>
                    <li class="list-group-item active">John Doe (Headteacher)</li>
                </ul>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    Chat with John Doe
                </div>
                <div class="card-body" style="height: 400px; overflow-y: scroll;">
                    <!-- Chat messages will appear here -->
                    <div class="alert alert-info">Welcome to the chat! This feature is powered by Pusher for real-time communication.</div>
                </div>
                <div class="card-footer">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Type a message...">
                        <button class="btn btn-primary" type="button">Send</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once APP_ROOT . '/includes/footer.php';
?>

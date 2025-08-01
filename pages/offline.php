<?php
/**
 * EduPulse - Offline Mode Page
 *
 * Explains the offline capabilities and registers the service worker.
 */

$page_title = "Offline Mode";
check_permission(['Superadmin', 'Headteacher', 'Deputy Headteacher', 'DOS', 'Bursar', 'Teacher', 'Student', 'Parent']);

require_once APP_ROOT . '/includes/header.php';
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Offline Capabilities</h1>

    <div class="card shadow">
        <div class="card-body">
            <h5 class="card-title">Working Offline with EduPulse</h5>
            <p>EduPulse is designed with offline capabilities to help you continue working even with an unstable internet connection.</p>

            <h6>How it Works:</h6>
            <ul>
                <li><strong>Caching:</strong> Key application pages and assets are stored on your device. This allows you to load the application interface even without an internet connection.</li>
                <li><strong>Offline Data:</strong> Information like your dashboard summary, recent marks, and class lists can be stored locally in your browser's storage (IndexedDB).</li>
                <li><strong>Syncing:</strong> When you perform an action while offline (like marking attendance), the data is saved locally. It will automatically sync with the server once your internet connection is restored.</li>
            </ul>

            <div id="offline-status" class="alert alert-info">
                Checking offline status...
            </div>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Register the Service Worker
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/edupulse/sw.js')
                .then(registration => {
                    console.log('ServiceWorker registration successful with scope: ', registration.scope);
                })
                .catch(error => {
                    console.log('ServiceWorker registration failed: ', error);
                });
        });
    }

    // Update the status message based on connection
    const statusDiv = document.getElementById('offline-status');
    function updateOnlineStatus() {
        if (navigator.onLine) {
            statusDiv.className = 'alert alert-success';
            statusDiv.textContent = 'You are currently online. All features are available.';
        } else {
            statusDiv.className = 'alert alert-warning';
            statusDiv.textContent = 'You are currently offline. Some features may be limited, but you can continue to work with cached data.';
        }
    }

    window.addEventListener('online', updateOnlineStatus);
    window.addEventListener('offline', updateOnlineStatus);
    updateOnlineStatus();
});
</script>

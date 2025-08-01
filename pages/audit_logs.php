<?php
/**
 * EduPulse - Audit Logs Page
 *
 * Displays a record of important actions taken within the system.
 */

$page_title = "Audit Logs";
check_permission(['Superadmin', 'Headteacher']);
$currentUser = get_current_user();
$userRole = $currentUser['role'];

// Fetch audit logs
try {
    if ($userRole === 'Superadmin') {
        // Superadmin sees all logs
        $stmt = $pdo->query("SELECT a.*, u.email as user_email FROM audit_logs a LEFT JOIN users u ON a.user_id = u.user_id ORDER BY a.timestamp DESC LIMIT 100");
    } else {
        // Headteacher sees logs for their school.
        // NOTE: This assumes a `school_id` column exists on the `audit_logs` table for proper multi-tenancy.
        // As it's not in the original schema, this query would need adjustment or the schema updated.
        // For now, we will fetch logs where the user performing the action belongs to the school.
        $stmt = $pdo->prepare(
           "SELECT a.*, u.email as user_email
            FROM audit_logs a
            JOIN users u ON a.user_id = u.user_id
            WHERE u.school_id = :school_id
            ORDER BY a.timestamp DESC LIMIT 100"
        );
        $stmt->execute(['school_id' => $currentUser['school_id']]);
    }
    $logs = $stmt->fetchAll();
} catch (PDOException $e) {
    $logs = [];
    $error_message = "Error fetching audit logs.";
}

require_once APP_ROOT . '/includes/header.php';
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">System Audit Logs</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Recent System Activity</h6>
        </div>
        <div class="card-body">
             <!-- Filter controls would go here -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Timestamp</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Details</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)): ?>
                            <tr><td colspan="5" class="text-center">No audit logs found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><?= date('Y-m-d H:i:s', strtotime($log['timestamp'])) ?></td>
                                <td><?= sanitize($log['user_email'] ?? 'System') ?></td>
                                <td><span class="badge bg-secondary"><?= sanitize($log['action']) ?></span></td>
                                <td><?= sanitize($log['details']) ?></td>
                                <td><?= sanitize($log['ip_address']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/includes/footer.php'; ?>

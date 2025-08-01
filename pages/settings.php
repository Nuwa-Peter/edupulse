<?php
/**
 * EduPulse - Settings Page
 *
 * Allows users to manage account and application settings based on their role.
 */

$page_title = "Settings";
check_permission(['Superadmin', 'Headteacher', 'Teacher', 'Student', 'Parent']); // All logged-in users
$currentUser = get_current_user();
$userRole = $currentUser['role'];

$error_message = '';
$success_message = '';

// Handle settings form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error_message = "Invalid request.";
    } else {
        // Placeholder for processing form data
        // e.g., if(isset($_POST['save_theme'])) { ... }
        $success_message = "Settings saved successfully (Placeholder).";
    }
}


require_once APP_ROOT . '/includes/header.php';
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Settings</h1>

    <?php if ($error_message) echo "<div class='alert alert-danger'>$error_message</div>"; ?>
    <?php if ($success_message) echo "<div class='alert alert-success'>$success_message</div>"; ?>

    <div class="row">
        <div class="col-lg-8">
            <!-- General Settings Card -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">General Settings</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                        <div class="mb-3 row">
                            <label for="theme" class="col-sm-3 col-form-label">UI Theme</label>
                            <div class="col-sm-9">
                                <select class="form-select" id="themeSelector">
                                    <option value="light">Light Theme</option>
                                    <option value="dark">Dark Theme</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="language" class="col-sm-3 col-form-label">Language</label>
                            <div class="col-sm-9">
                                <select class="form-select" id="language" name="language">
                                    <option value="en">English</option>
                                    <option value="lg" disabled>Luganda (Coming Soon)</option>
                                    <option value="sw" disabled>Swahili (Coming Soon)</option>
                                    <option value="run" disabled>Runyankole (Coming Soon)</option>
                                </select>
                            </div>
                        </div>
                         <button type="submit" name="save_general" class="btn btn-primary">Save General Settings</button>
                    </form>
                </div>
            </div>

            <!-- Headteacher-specific Settings -->
            <?php if ($userRole === 'Headteacher'): ?>
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">School Academic Settings</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                        <div class="mb-3">
                            <label class="form-label">Term 1 Dates</label>
                            <div class="input-group">
                                <input type="date" class="form-control" name="term1_start">
                                <span class="input-group-text">to</span>
                                <input type="date" class="form-control" name="term1_end">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Term 2 Dates</label>
                            <div class="input-group">
                                <input type="date" class="form-control" name="term2_start">
                                <span class="input-group-text">to</span>
                                <input type="date" class="form-control" name="term2_end">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Term 3 Dates</label>
                            <div class="input-group">
                                <input type="date" class="form-control" name="term3_start">
                                <span class="input-group-text">to</span>
                                <input type="date" class="form-control" name="term3_end">
                            </div>
                        </div>
                        <button type="submit" name="save_academic" class="btn btn-primary">Save Academic Year</button>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <!-- Superadmin-specific Settings -->
            <?php if ($userRole === 'Superadmin'): ?>
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">System Administration</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="maintenanceMode" name="maintenance_mode">
                            <label class="form-check-label" for="maintenanceMode">Enable Maintenance Mode</label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">System-wide Announcement</label>
                            <textarea class="form-control" name="system_announcement" rows="3"></textarea>
                        </div>
                        <button type="submit" name="save_system" class="btn btn-danger">Save System Settings</button>
                    </form>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/includes/footer.php'; ?>

<script>
// Basic theme switcher using localStorage
document.addEventListener('DOMContentLoaded', function() {
    const themeSelector = document.getElementById('themeSelector');
    const currentTheme = localStorage.getItem('theme') || 'light';

    document.body.setAttribute('data-theme', currentTheme);
    themeSelector.value = currentTheme;

    themeSelector.addEventListener('change', function() {
        const selectedTheme = this.value;
        localStorage.setItem('theme', selectedTheme);
        document.body.setAttribute('data-theme', selectedTheme);
        // Add CSS in custom.css to handle the [data-theme="dark"] selector
    });
});
</script>

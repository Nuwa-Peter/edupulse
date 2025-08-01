<?php
/**
 * EduPulse - Dashboard Page
 *
 * This is the main landing page for users after they log in.
 * The content is dynamically generated based on the user's role.
 */

$page_title = "Dashboard";

// Ensure the user is logged in and has a role.
check_permission(['Superadmin', 'Headteacher', 'Deputy Headteacher', 'DOS', 'Bursar', 'Teacher', 'Student', 'Parent']);
$currentUser = get_current_user();
$userRole = $currentUser['role'];

// Include the header
require_once APP_ROOT . '/includes/header.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
    </div>

    <!-- Display any session-based error messages -->
    <?php display_flash_message('error_message', 'alert-danger'); ?>
    <?php display_flash_message('success_message', 'alert-success'); ?>

    <!-- Role-Specific Content -->
    <?php switch ($userRole):

        // ===================================================================
        // SUPERADMIN DASHBOARD
        // ===================================================================
        case 'Superadmin': ?>
            <div class="row">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Schools</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">2</div> <!-- Placeholder -->
                                </div>
                                <div class="col-auto"><i class="fas fa-school fa-2x text-gray-300"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Users</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">9</div> <!-- Placeholder -->
                                </div>
                                <div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <p>Welcome, Superadmin. From here you can manage all schools, users, and system settings.</p>
            <?php break; ?>

        <?php
        // ===================================================================
        // HEADTEACHER DASHBOARD
        // ===================================================================
        case 'Headteacher': ?>
            <!-- Story Bar Placeholder -->
            <h5>Stories from Teachers</h5>
            <div class="story-bar card p-2 mb-4">
                <div class="story">
                    <img src="<?= BASE_URL ?>/assets/images/placeholder.png" class="story-photo" alt="Teacher Story">
                    <div class="story-user small">Alice N.</div>
                </div>
                <!-- More stories would be loaded here -->
            </div>

            <div class="row">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Students</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">150</div> <!-- Placeholder -->
                                </div>
                                <div class="col-auto"><i class="fas fa-user-graduate fa-2x text-gray-300"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                 <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Fees Collected (Term 1)</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">UGX 50,000,000</div> <!-- Placeholder -->
                                </div>
                                <div class="col-auto"><i class="fas fa-dollar-sign fa-2x text-gray-300"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Student Gender Distribution</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="genderPieChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <?php break; ?>

        <?php
        // ===================================================================
        // TEACHER DASHBOARD
        // ===================================================================
        case 'Teacher': ?>
            <p>Welcome, Teacher. Here is a summary of your classes and tasks.</p>
            <!-- Teacher specific widgets would go here -->
            <?php break; ?>

        <?php
        // ===================================================================
        // STUDENT DASHBOARD
        // ===================================================================
        case 'Student': ?>
            <p>Welcome, Student. View your academic progress and school activities here.</p>
            <!-- Student specific widgets would go here -->
            <?php break; ?>

        <?php
        // ===================================================================
        // PARENT DASHBOARD
        // ===================================================================
        case 'Parent': ?>
            <p>Welcome, Parent. Keep track of your child's performance and school updates.</p>
            <!-- Parent specific widgets would go here -->
            <?php break; ?>

        <?php default: ?>
            <div class="alert alert-info">Welcome to your EduPulse dashboard.</div>
    <?php endswitch; ?>

</div>

<?php
// Include the footer
require_once APP_ROOT . '/includes/footer.php';
?>

<!-- Page-specific scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart.js Example for Headteacher Dashboard
    <?php if ($userRole === 'Headteacher'): ?>
    const ctx = document.getElementById('genderPieChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Male', 'Female'],
                datasets: [{
                    label: 'Number of Students',
                    // Data should be fetched dynamically from the database
                    data: [80, 70], // Placeholder data
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 99, 132, 0.8)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 99, 132, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                }
            }
        });
    }
    <?php endif; ?>
});
</script>

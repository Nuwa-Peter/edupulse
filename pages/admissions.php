<?php
/**
 * EduPulse - Online Admissions Page
 *
 * Provides a public form for parents to submit applications and a
 * dashboard for Headteachers to review and approve them.
 */

$page_title = "Admissions";
// This page has public and private views, so permission check is done inside.
$currentUser = get_current_user();
$is_headteacher = (is_logged_in() && $currentUser['role'] === 'Headteacher');

if ($is_headteacher) {
    // Fetch pending admissions for the Headteacher's school
    try {
        $stmt = $pdo->prepare("SELECT * FROM admissions WHERE school_id = :school_id AND status = 'pending' ORDER BY submitted_at DESC");
        $stmt->execute(['school_id' => $currentUser['school_id']]);
        $applications = $stmt->fetchAll();
    } catch (PDOException $e) {
        $applications = [];
        $error_message = "Error fetching applications.";
    }
}

require_once APP_ROOT . '/includes/header.php';
?>

<div class="container-fluid">

    <?php if ($is_headteacher): ?>
    <!-- ================== HEADTEACHER VIEW ================== -->
    <h1 class="h3 mb-4 text-gray-800">Review Admission Applications</h1>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Pending Applications</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead><tr><th>Applicant Name</th><th>Date of Birth</th><th>Previous School</th><th>Parent Contact</th><th>Submitted On</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php if (empty($applications)): ?>
                            <tr><td colspan="6" class="text-center">No pending applications.</td></tr>
                        <?php else: ?>
                            <?php foreach ($applications as $app): ?>
                            <tr>
                                <td><?= sanitize($app['student_name']) ?></td>
                                <td><?= sanitize($app['date_of_birth']) ?></td>
                                <td><?= sanitize($app['previous_school']) ?></td>
                                <td><?= sanitize($app['parent_contact']) ?></td>
                                <td><?= date('Y-m-d', strtotime($app['submitted_at'])) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-success">Approve</button>
                                    <button class="btn btn-sm btn-danger">Reject</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php else: ?>
    <!-- ================== PUBLIC VIEW ================== -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow my-5">
                <div class="card-header text-center fs-4">
                    <h1 class="h3 text-gray-800">Online Admission Application</h1>
                </div>
                <div class="card-body p-4">
                    <p>Please fill out the form below to apply for admission. Our team will review your application and get back to you.</p>
                    <form method="POST" action="">
                        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">

                        <h5 class="text-primary mt-3">Prospective Student's Information</h5>
                        <hr>
                        <div class="row">
                            <div class="col-md-6 mb-3"><label class="form-label">Full Name</label><input type="text" name="student_name" class="form-control" required></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Date of Birth</label><input type="date" name="student_dob" class="form-control" required></div>
                        </div>
                        <div class="mb-3"><label class="form-label">Previous School (if any)</label><input type="text" name="previous_school" class="form-control"></div>

                        <h5 class="text-primary mt-4">Parent/Guardian Information</h5>
                        <hr>
                         <div class="row">
                            <div class="col-md-6 mb-3"><label class="form-label">Full Name</label><input type="text" name="parent_name" class="form-control" required></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Relationship to Student</label><input type="text" name="parent_relation" class="form-control" required></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3"><label class="form-label">Contact Email</label><input type="email" name="parent_email" class="form-control" required></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Contact Phone</label><input type="tel" name="parent_phone" class="form-control" required></div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" name="submit_application" class="btn btn-primary btn-lg">Submit Application</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once APP_ROOT . '/includes/footer.php'; ?>

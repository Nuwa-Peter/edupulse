<?php
/**
 * EduPulse - Admin Access Management Page
 *
 * Superadmin manages Headteachers.
 * Headteacher manages their school's admin staff (DOS, Bursar, etc.).
 */

$page_title = "Manage Admin Access";
check_permission(['Superadmin', 'Headteacher']);
$currentUser = get_current_user();
$userRole = $currentUser['role'];

// Fetch relevant users based on role
try {
    if ($userRole === 'Superadmin') {
        $stmt = $pdo->prepare("SELECT u.user_id, u.first_name, u.last_name, u.email, u.status, s.name as school_name
                               FROM users u
                               JOIN schools s ON u.school_id = s.school_id
                               WHERE u.role = 'Headteacher'");
        $stmt->execute();
        $admins = $stmt->fetchAll();
    } else { // Headteacher
        $stmt = $pdo->prepare("SELECT user_id, first_name, last_name, email, role, status
                               FROM users
                               WHERE school_id = :school_id AND role IN ('Deputy Headteacher', 'DOS', 'Bursar')");
        $stmt->execute(['school_id' => $currentUser['school_id']]);
        $admins = $stmt->fetchAll();
    }
} catch (PDOException $e) {
    $admins = [];
    $error_message = "Error fetching admin users.";
}


require_once APP_ROOT . '/includes/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manage Administrative Access</h1>
        <?php if ($userRole === 'Headteacher'): ?>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#grantAccessModal"><i class="fas fa-user-shield me-2"></i>Grant Access</button>
        <?php endif; ?>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <?= $userRole === 'Superadmin' ? 'All Headteachers' : 'Your School\'s Admin Staff' ?>
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <?php if ($userRole === 'Superadmin'): ?><th>School</th><?php endif; ?>
                            <th>Role</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($admins as $admin): ?>
                        <tr>
                            <td><?= sanitize($admin['first_name'] . ' ' . $admin['last_name']) ?></td>
                             <?php if ($userRole === 'Superadmin'): ?><td><?= sanitize($admin['school_name']) ?></td><?php endif; ?>
                            <td><?= sanitize($admin['role']) ?></td>
                            <td><?= sanitize($admin['email']) ?></td>
                            <td><span class="badge bg-<?= $admin['status'] === 'active' ? 'success' : 'secondary' ?>"><?= ucfirst($admin['status']) ?></span></td>
                            <td>
                                <button class="btn btn-sm btn-warning">Edit</button>
                                <button class="btn btn-sm btn-danger">Revoke</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Grant Access Modal (for Headteacher) -->
<div class="modal fade" id="grantAccessModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Grant Administrative Access</h5></div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">Select Teacher to Promote</label>
                        <!-- Dropdown of teachers would be populated here -->
                        <select class="form-select"></select>
                    </div>
                     <div class="mb-3">
                        <label class="form-label">Select Role to Grant</label>
                        <select class="form-select">
                            <option value="Deputy Headteacher">Deputy Headteacher</option>
                            <option value="DOS">Director of Studies (DOS)</option>
                            <option value="Bursar">Bursar</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Grant Access</button>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/includes/footer.php'; ?>

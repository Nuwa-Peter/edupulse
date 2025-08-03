<?php
/**
 * EduPulse - Marks Management
 */

require_once __DIR__ . '/../includes/config.php';

// --- Authentication Check ---
if (!is_logged_in() || !in_array(get_current_user()['role'], ['Headteacher', 'DOS', 'Teacher'])) {
    redirect('/index.php?route=login');
}

$page_title = "Marks Management";
require_once APP_ROOT . '/includes/header.php';

// Placeholder data
$students = [
    ['id' => 4, 'name' => 'Peter Jones'],
];
$subjects = [
    ['id' => 1, 'name' => 'Mathematics'],
    ['id' => 2, 'name' => 'English'],
];

?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Enter Student Marks</h1>
    <div>
        <a href="#" class="btn btn-sm btn-success shadow-sm"><i class="fas fa-file-excel fa-sm"></i> Import Marks</a>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Select Class and Subject</h6>
    </div>
    <div class="card-body">
        <form>
            <div class="row">
                <div class="col-md-4">
                    <label for="class_select" class="form-label">Class</label>
                    <select id="class_select" class="form-select">
                        <option selected>Choose...</option>
                        <option>P.1 Blue</option>
                        <option>S.1 A</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="subject_select" class="form-label">Subject</label>
                    <select id="subject_select" class="form-select">
                        <option selected>Choose...</option>
                        <option>Mathematics</option>
                        <option>English</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Load Students</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Marks Entry Sheet for S.1 A - Mathematics</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Beginning of Term (30%)</th>
                        <th>Mid Term (30%)</th>
                        <th>End of Term (40%)</th>
                        <th>Total (100%)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?= sanitize($student['name']) ?></td>
                            <td><input type="number" class="form-control" max="30"></td>
                            <td><input type="number" class="form-control" max="30"></td>
                            <td><input type="number" class="form-control" max="40"></td>
                            <td><strong>0</strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <button class="btn btn-success">Save Marks</button>
    </div>
</div>


<?php
require_once APP_ROOT . '/includes/footer.php';
?>

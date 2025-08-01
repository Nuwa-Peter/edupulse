<?php
/**
 * EduPulse - School Directory Page
 *
 * Displays a directory of all schools using the system.
 */

$page_title = "School Directory";
check_permission(['Superadmin']); // Primarily for Superadmin

// Fetch all schools
try {
    $stmt = $pdo->query("SELECT school_id, edupulse_id, name, level, address, latitude, longitude FROM schools ORDER BY name");
    $schools = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $schools = [];
    $error_message = "Error fetching schools.";
}

require_once APP_ROOT . '/includes/header.php';
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">School Directory</h1>

    <!-- Map View -->
    <div class="card shadow mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Map of All Schools</h6>
        </div>
        <div class="card-body p-0" style="height: 400px;">
            <div id="directoryMap" style="height: 100%;"></div>
        </div>
    </div>

    <!-- Directory List -->
    <div class="card shadow mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">All Registered Schools</h6>
        </div>
        <div class="card-body">
            <!-- Filter/Search controls would go here -->
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>School Name</th>
                            <th>Level</th>
                            <th>Address</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($schools as $school): ?>
                        <tr>
                            <td><?= sanitize($school['name']) ?></td>
                            <td><?= sanitize($school['level']) ?></td>
                            <td><?= sanitize($school['address']) ?></td>
                            <td>
                                <!-- This would require logic to view another school's info page -->
                                <a href="#" class="btn btn-sm btn-info">View Details</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const schoolsData = <?= json_encode($schools) ?>;

    if (schoolsData.length > 0) {
        // Find a central point or default to Uganda
        const map = L.map('directoryMap').setView([1.3733, 32.2903], 7);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        schoolsData.forEach(school => {
            if (school.latitude && school.longitude) {
                L.marker([school.latitude, school.longitude]).addTo(map)
                    .bindPopup(`<b>${school.name}</b><br>${school.level}`);
            }
        });
    } else {
         document.getElementById('directoryMap').innerHTML = '<div class="alert alert-warning m-3">No schools with location data to display on map.</div>';
    }
});
</script>

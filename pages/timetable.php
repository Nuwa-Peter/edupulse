<?php
/**
 * EduPulse - Timetable Management Page
 *
 * Allows users to view, create, and manage class timetables.
 */

$page_title = "Timetable";
check_permission(['DOS', 'Teacher', 'Student']);
$currentUser = get_current_user();
$school_id = $currentUser['school_id'];

// Fetch classes for the dropdown
try {
    $classes_stmt = $pdo->prepare("SELECT class_id, class_name FROM classes WHERE school_id = :school_id ORDER BY class_name");
    $classes_stmt->execute(['school_id' => $school_id]);
    $classes = $classes_stmt->fetchAll();
} catch (PDOException $e) {
    $classes = [];
    $error_message = "Error fetching classes.";
}

require_once APP_ROOT . '/includes/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Class Timetable</h1>
        <div>
            <button id="exportPdfBtn" class="btn btn-secondary"><i class="fas fa-file-pdf me-2"></i>Export as PDF</button>
            <?php if ($currentUser['role'] === 'DOS'): ?>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEventModal"><i class="fas fa-plus me-2"></i>Add Timetable Event</button>
            <?php endif; ?>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="row align-items-center">
                <div class="col-md-4">
                     <label for="class_selector" class="form-label">Select Class to View:</label>
                     <select id="class_selector" class="form-select">
                        <option value="">Choose a class...</option>
                        <?php foreach ($classes as $class): ?>
                        <option value="<?= $class['class_id'] ?>"><?= sanitize($class['class_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div id="calendar"></div>
        </div>
    </div>
</div>

<!-- Add Event Modal (for DOS) -->
<?php if ($currentUser['role'] === 'DOS'): ?>
<div class="modal fade" id="addEventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Timetable Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addEventForm">
                    <!-- Form fields for subject, teacher, day, time would go here -->
                    <p>Form to add a new lesson to the timetable. Should include conflict detection logic.</p>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="saveEventBtn" class="btn btn-primary">Save Event</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>


<?php require_once APP_ROOT . '/includes/footer.php'; ?>

<!-- Page-specific scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek', // Weekly view is standard for timetables
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'timeGridWeek,timeGridDay'
        },
        slotMinTime: '08:00:00', // School start time
        slotMaxTime: '17:00:00', // School end time
        weekends: false, // Most schools don't have weekend classes
        allDaySlot: false,
        events: [
            // Dummy event data. This should be loaded via AJAX based on class selection.
            {
                title: 'Mathematics - Mr. Byamukama',
                start: '2025-08-04T09:00:00',
                end: '2025-08-04T10:00:00'
            },
            {
                title: 'English - Ms. Nankya',
                start: '2025-08-05T11:00:00',
                end: '2025-08-05T12:00:00'
            }
        ]
    });
    calendar.render();

    // Add logic to refetch events when class_selector changes
    $('#class_selector').on('change', function() {
        const classId = $(this).val();
        if(classId) {
            // calendar.getEventSources().forEach(source => source.remove());
            // calendar.addEventSource('/api/timetable-events?class_id=' + classId);
            alert('AJAX call to fetch timetable for class ' + classId + ' would happen here.');
        }
    });
});
</script>

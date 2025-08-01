<?php
/**
 * EduPulse - Sidebar Navigation Links
 *
 * This file generates the role-specific navigation links for the sidebar.
 * It is included by header.php.
 *
 * It expects $userRole to be defined.
 */

if (!isset($userRole)) {
    return;
}

switch ($userRole) {
    // --- Superadmin Role ---
    case 'Superadmin':
        echo '<li><a href="' . BASE_URL . '/directory"><i class="fas fa-school me-2"></i>School Directory</a></li>';
        echo '<li><a href="' . BASE_URL . '/admin-access"><i class="fas fa-user-shield me-2"></i>Manage Admins</a></li>';
        echo '<li><a href="' . BASE_URL . '/backups"><i class="fas fa-database me-2"></i>System Backups</a></li>';
        echo '<li><a href="' . BASE_URL . '/audit-logs"><i class="fas fa-file-alt me-2"></i>Audit Logs</a></li>';
        break;

    // --- Headteacher Role ---
    case 'Headteacher':
        echo '<li><a href="' . BASE_URL . '/analytics"><i class="fas fa-chart-pie me-2"></i>School Analytics</a></li>';
        echo '<li><a href="' . BASE_URL . '/teachers"><i class="fas fa-chalkboard-teacher me-2"></i>Manage Teachers</a></li>';
        echo '<li><a href="' . BASE_URL . '/students"><i class="fas fa-user-graduate me-2"></i>Manage Students</a></li>';
        echo '<li><a href="' . BASE_URL . '/admissions"><i class="fas fa-user-plus me-2"></i>Admissions</a></li>';
        echo '<li><a href="' . BASE_URL . '/fees"><i class="fas fa-dollar-sign me-2"></i>Fee Management</a></li>';
        echo '<li><a href="' . BASE_URL . '/chat"><i class="fas fa-comments me-2"></i>Chat</a></li>';
        echo '<li><a href="' . BASE_URL . '/surveys"><i class="fas fa-poll me-2"></i>Feedback Surveys</a></li>';
        echo '<li><a href="' . BASE_URL . '/announcements"><i class="fas fa-bullhorn me-2"></i>Announcements</a></li>';
        break;

    // --- DOS (Director of Studies) Role ---
    case 'DOS':
        echo '<li><a href="' . BASE_URL . '/classes"><i class="fas fa-chalkboard me-2"></i>Classes & Subjects</a></li>';
        echo '<li><a href="' . BASE_URL . '/marks"><i class="fas fa-marker me-2"></i>Enter Marks</a></li>';
        echo '<li><a href="' . BASE_URL . '/report-card"><i class="fas fa-file-invoice me-2"></i>Report Cards</a></li>';
        echo '<li><a href="' . BASE_URL . '/student-id"><i class="fas fa-id-card me-2"></i>Generate IDs</a></li>';
        echo '<li><a href="' . BASE_URL . '/timetable"><i class="fas fa-calendar-alt me-2"></i>Timetable</a></li>';
        echo '<li><a href="' . BASE_URL . '/attendance"><i class="fas fa-user-check me-2"></i>Attendance</a></li>';
        echo '<li><a href="' . BASE_URL . '/aoi"><i class="fas fa-puzzle-piece me-2"></i>AOI</a></li>';
        break;

    // --- Bursar Role ---
    case 'Bursar':
        echo '<li><a href="' . BASE_URL . '/fees"><i class="fas fa-money-bill-wave me-2"></i>Manage Fees</a></li>';
        // Add financial reports link later
        break;

    // --- Teacher Role ---
    case 'Teacher':
        echo '<li><a href="' . BASE_URL . '/analytics"><i class="fas fa-chart-bar me-2"></i>Class Analytics</a></li>';
        echo '<li><a href="' . BASE_URL . '/marks"><i class="fas fa-marker me-2"></i>My Classes Marks</a></li>';
        echo '<li><a href="' . BASE_URL . '/attendance"><i class="fas fa-user-check me-2"></i>Mark Attendance</a></li>';
        echo '<li><a href="' . BASE_URL . '/elearning"><i class="fas fa-book-open me-2"></i>E-Learning</a></li>';
        echo '<li><a href="' . BASE_URL . '/chat"><i class="fas fa-comments me-2"></i>Chat with HT</a></li>';
        echo '<li><a href="' . BASE_URL . '/aoi"><i class="fas fa-puzzle-piece me-2"></i>My AOIs</a></li>';
        break;

    // --- Student Role ---
    case 'Student':
        echo '<li><a href="' . BASE_URL . '/report-card"><i class="fas fa-file-alt me-2"></i>My Report Card</a></li>';
        echo '<li><a href="' . BASE_URL . '/student-id"><i class="fas fa-id-card me-2"></i>My ID Card</a></li>';
        echo '<li><a href="' . BASE_URL . '/timetable"><i class="fas fa-calendar me-2"></i>My Timetable</a></li>';
        echo '<li><a href="' . BASE_URL . '/elearning"><i class="fas fa-book me-2"></i>E-Learning Materials</a></li>';
        echo '<li><a href="' . BASE_URL . '/aoi"><i class="fas fa-running me-2"></i>Join AOI</a></li>';
        echo '<li><a href="' . BASE_URL . '/analytics"><i class="fas fa-brain me-2"></i>AI Study Plan</a></li>';
        break;

    // --- Parent Role ---
    case 'Parent':
        echo '<li><a href="' . BASE_URL . '/parent-portal"><i class="fas fa-child me-2"></i>My Child\'s Dashboard</a></li>';
        echo '<li><a href="' . BASE_URL . '/fees"><i class="fas fa-money-check-alt me-2"></i>Fee Payments</a></li>';
        echo '<li><a href="' . BASE_URL . '/surveys"><i class="fas fa-poll-h me-2"></i>Feedback Surveys</a></li>';
        echo '<li><a href="' . BASE_URL . '/announcements"><i class="fas fa-bullhorn me-2"></i>School Announcements</a></li>';
        break;
}
?>

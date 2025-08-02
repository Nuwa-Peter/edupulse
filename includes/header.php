<?php
/**
 * EduPulse - Main Header File
 *
 * This file contains the opening HTML structure, the <head> section with all
 * CSS/JS library includes, the main navigation bar, and the sidebar. It should
 * be included at the top of every user-facing page.
 *
 * It relies on functions and variables defined in config.php, such as:
 * - $pdo: The database connection object.
 * - is_logged_in(), get_current_user(): Session management functions.
 * - BASE_URL: The base URL of the application.
 */

// config.php should already be included by the router (index.php)
if (!defined('BASE_URL')) {
    die('Error: Core configuration is not loaded.');
}

$currentUser = get_current_user();
$userRole = $currentUser['role'] ?? 'Guest';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduPulse - School Management System</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>/assets/images/edupulse_logo_logo.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Core CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Third-Party CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/custom.css">

</head>
<body>

<!-- Global Header for Theme Switcher -->
<div class="global-header-bar py-2 px-3 bg-light border-bottom">
    <div class="d-flex justify-content-end">
        <!-- Theme Switcher Dropdown -->
        <div class="dropdown">
            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="themeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-sun" id="theme-icon"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="themeDropdown">
                <li><button class="dropdown-item" type="button" data-theme-value="light"><i class="fas fa-sun me-2"></i>Light</button></li>
                <li><button class="dropdown-item" type="button" data-theme-value="dark"><i class="fas fa-moon me-2"></i>Dark</button></li>
                <li><button class="dropdown-item" type="button" data-theme-value="auto"><i class="fas fa-desktop me-2"></i>Auto</button></li>
            </ul>
        </div>
    </div>
</div>


<div class="wrapper">
    <!-- Sidebar -->
    <?php if (is_logged_in()): ?>
    <nav id="sidebar">
        <div class="sidebar-header">
            <img src="<?= BASE_URL ?>/assets/images/edupulse_logo_logo.png" alt="EduPulse Logo" class="edupulse-logo-sidebar">
            <h3>EduPulse</h3>
            <p class="text-light small"><?= htmlspecialchars($currentUser['school_name'] ?? 'Superadmin Panel'); ?></p>
        </div>

        <ul class="list-unstyled components">
            <li class="active"><a href="<?= BASE_URL ?>/dashboard"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>

            <!-- Role-based Navigation -->
            <?php require_once __DIR__ . '/sidebar_links.php'; ?>

            <li><a href="<?= BASE_URL ?>/logout"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
        </ul>
    </nav>
    <?php endif; ?>

    <!-- Page Content -->
    <div id="content" class="<?php if (!is_logged_in()) echo 'w-100 p-0'; // Full width for login page ?>">
        <?php if (is_logged_in()): ?>
        <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
            <div class="container-fluid">

                <button type="button" id="sidebarCollapse" class="btn btn-primary">
                    <i class="fas fa-align-left"></i>
                    <span>Toggle Sidebar</span>
                </button>

                <form class="d-flex search-bar ms-auto">
                    <input class="form-control me-2" type="search" placeholder="Search students, teachers..." aria-label="Search">
                    <button class="btn btn-outline-primary" type="submit"><i class="fas fa-search"></i></button>
                </form>

                <div class="ms-3 d-flex align-items-center">
                    <!-- User Profile Dropdown -->
                    <div class="dropdown user-profile-dropdown">
                        <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?= BASE_URL . '/' . htmlspecialchars($currentUser['profile_photo_url'] ?? 'assets/images/placeholder.png') ?>" alt="" class="me-2">
                            <strong><?= htmlspecialchars($currentUser['first_name'] ?? 'User'); ?></strong>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end text-small shadow" aria-labelledby="dropdownUser">
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/profile"><i class="fas fa-user-circle me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/settings"><i class="fas fa-cog me-2"></i>Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/logout"><i class="fas fa-sign-out-alt me-2"></i>Sign out</a></li>
                        </ul>
                    </div>
                </div>

            </div>
        </nav>
        <?php endif; ?>

        <!-- The main content of the page will be rendered here -->
        <main class="main-content">



<!-- The closing tags for main, body, html and script includes are in footer.php -->

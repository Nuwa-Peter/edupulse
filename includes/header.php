<?php
/**
 * EduPulse - Global Header
 *
 * This file is included at the top of all user-facing pages.
 * It contains the HTML head, links to stylesheets, and the main navigation bar.
 */

// config.php and functions.php are already included by the page that calls this header.
$currentUser = get_current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title . ' - ' : '' ?>EduPulse</title>

    <!-- CDNs -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/custom.css">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body class="<?= isset($body_class) ? $body_class : '' ?>">

<div class="d-flex" id="wrapper">
    <!-- Sidebar -->
    <?php if (is_logged_in()): ?>
    <div class="bg-light border-right" id="sidebar-wrapper">
        <div class="sidebar-heading">
            <a href="<?= BASE_URL ?>/index.php?route=dashboard">
                <img src="<?= BASE_URL ?>/Uploads/logos/edupulse_logo.png" alt="EduPulse Logo" style="height: 40px;">
                EduPulse
            </a>
        </div>
        <div class="list-group list-group-flush">
            <a href="<?= BASE_URL ?>/index.php?route=dashboard" class="list-group-item list-group-item-action bg-light">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
            <a href="<?= BASE_URL ?>/index.php?route=students" class="list-group-item list-group-item-action bg-light">
                <i class="fas fa-user-graduate me-2"></i>Students
            </a>
             <a href="<?= BASE_URL ?>/index.php?route=teachers" class="list-group-item list-group-item-action bg-light">
                <i class="fas fa-chalkboard-teacher me-2"></i>Teachers
            </a>
            <a href="<?= BASE_URL ?>/index.php?route=classes" class="list-group-item list-group-item-action bg-light">
                <i class="fas fa-school me-2"></i>Classes
            </a>
            <a href="<?= BASE_URL ?>/index.php?route=marks" class="list-group-item list-group-item-action bg-light">
                <i class="fas fa-marker me-2"></i>Marks
            </a>
            <a href="<?= BASE_URL ?>/index.php?route=fees" class="list-group-item list-group-item-action bg-light">
                <i class="fas fa-money-bill-wave me-2"></i>Fees
            </a>
            <a href="<?= BASE_URL ?>/index.php?route=chat" class="list-group-item list-group-item-action bg-light">
                <i class="fas fa-comments me-2"></i>Chat
            </a>
             <a href="<?= BASE_URL ?>/index.php?route=analytics" class="list-group-item list-group-item-action bg-light">
                <i class="fas fa-chart-line me-2"></i>Analytics
            </a>
        </div>
    </div>
    <?php endif; ?>
    <!-- /#sidebar-wrapper -->

    <!-- Page Content -->
    <div id="page-content-wrapper">
        <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
            <div class="container-fluid">
                <?php if (is_logged_in()): ?>
                    <button class="btn btn-primary" id="menu-toggle"><i class="fas fa-bars"></i></button>
                <?php else: ?>
                    <a class="navbar-brand" href="<?= BASE_URL ?>">
                        <img src="<?= BASE_URL ?>/Uploads/logos/edupulse_logo.png" alt="EduPulse Logo" style="height: 30px;">
                        EduPulse
                    </a>
                <?php endif; ?>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                        <?php if (is_logged_in() && $currentUser): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <img src="<?= BASE_URL . '/' . $currentUser['profile_photo_url'] ?>" class="rounded-circle" style="height: 30px; width: 30px; object-fit: cover;" alt="Profile Photo">
                                    <?= $currentUser['first_name'] ?>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="<?= BASE_URL ?>/index.php?route=profile">Profile</a>
                                    <a class="dropdown-item" href="<?= BASE_URL ?>/index.php?route=settings">Settings</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="<?= BASE_URL ?>/index.php?route=logout">Logout</a>
                                </div>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= BASE_URL ?>/index.php?route=login">Login</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link btn btn-primary text-white" href="<?= BASE_URL ?>/index.php?route=register_school">Register School</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container-fluid p-4">
            <div id="content">
                <!-- Page-specific content will be loaded here -->

<?php
/**
 * EduPulse - Logout
 */

require_once __DIR__ . '/../includes/config.php';

// Destroy the session
session_destroy();

// Redirect to the login page
redirect('/index.php?route=login');
?>

<?php
/**
 * EduPulse - Send Email Backend Script
 *
 * This script handles the server-side logic for sending emails using PHPMailer.
 * It is intended to be called via AJAX.
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Manually include config and autoloader as this is an API-like endpoint
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Set content type to JSON
header('Content-Type: application/json');

// --- Response Object ---
$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

// --- Security Check ---
// In a real app, you would check permissions here, e.g., if the user is a Headteacher.
// if (!is_logged_in() || get_current_user()['role'] !== 'Headteacher') {
//     $response['message'] = 'Unauthorized';
//     echo json_encode($response);
//     exit();
// }

// --- Get POST Data ---
$recipient = $_POST['recipient'] ?? '';
$subject = $_POST['subject'] ?? 'No Subject';
$body = $_POST['body'] ?? '';

if (empty($recipient) || !filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
    $response['message'] = 'Invalid recipient email.';
    echo json_encode($response);
    exit();
}

// --- PHPMailer Logic ---
$mail = new PHPMailer(true);

try {
    // Server settings from config.php (placeholders)
    // $mail->isSMTP();
    // $mail->Host       = 'smtp.example.com';
    // $mail->SMTPAuth   = true;
    // $mail->Username   = 'user@example.com';
    // $mail->Password   = 'secret';
    // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    // $mail->Port       = 587;

    // For testing, we can use PHP's mail() function
    $mail->isMail();

    // Recipients
    $mail->setFrom('no-reply@edupulse.com', 'EduPulse System');
    $mail->addAddress($recipient);

    // Content
    $mail->isHTML(true);
    $mail->Subject = sanitize($subject);
    $mail->Body    = $body; // Assuming body is HTML; otherwise, sanitize
    $mail->AltBody = strip_tags($body);

    $mail->send();

    $response['status'] = 'success';
    $response['message'] = 'Email has been sent successfully!';

} catch (Exception $e) {
    $response['message'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

echo json_encode($response);
?>

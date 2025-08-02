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
// In a real app, you would check permissions here
if (!is_logged_in()) {
    $response['message'] = 'Unauthorized';
    echo json_encode($response);
    exit();
}

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
    // Server settings from config.php
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = SMTP_PORT;

    //Recipients
    $mail->setFrom(EMAIL_FROM_ADDRESS, EMAIL_FROM_NAME);
    $mail->addAddress($recipient);

    // Content
    $mail->isHTML(true);
    $mail->Subject = sanitize($subject);
    $mail->Body    = $body;
    $mail->AltBody = strip_tags($body);

    $mail->send();

    $response['status'] = 'success';
    $response['message'] = 'Email has been sent successfully!';

} catch (Exception $e) {
    $response['message'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

echo json_encode($response);
?>

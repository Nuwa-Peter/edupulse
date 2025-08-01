<?php
/**
 * EduPulse - Send SMS Backend Script
 *
 * This script handles the server-side logic for sending SMS messages
 * via a third-party SMS gateway API.
 */

// Manually include config
require_once dirname(__DIR__) . '/includes/config.php';

// Set content type to JSON
header('Content-Type: application/json');

// --- Response Object ---
$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

// --- Security Check ---
// check_permission(['Headteacher']); // Example permission check

// --- Get POST Data ---
$recipient_phone = $_POST['phone'] ?? '';
$message = $_POST['message'] ?? '';

// Basic validation
if (empty($recipient_phone) || empty($message)) {
    $response['message'] = 'Recipient phone number and message are required.';
    echo json_encode($response);
    exit();
}

// --- SMS Gateway Logic (Placeholder for a provider like AfricasTalking or Twilio) ---

// In a real application, you would get these from config.php
$gateway_username = 'YOUR_GATEWAY_USERNAME';
$gateway_api_key = 'YOUR_GATEWAY_API_KEY';

// The API endpoint for the gateway
$url = 'https://api.africastalking.com/version1/messaging'; // Example for AfricasTalking

$data = [
    'username' => $gateway_username,
    'to' => $recipient_phone,
    'message' => $message,
    // 'from' => 'EDUPULSE' // Optional sender ID
];

// Use cURL to send the request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/x-www-form-urlencoded',
    'apiKey: ' . $gateway_api_key
]);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// This is where the actual API call would be made.
// For this placeholder, we will simulate a successful response.
// $api_response = curl_exec($ch);
// $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
// curl_close($ch);

// --- Simulated Response ---
$simulated_http_code = 201;
// $simulated_api_response = '{"SMSMessageData":{"Message":"Sent to 1/1 Total Cost: KES 0.8000","Recipients":[{"statusCode":101,"number":"+254...","cost":"KES 0.8000","status":"Success","messageId":"ATX..._1"}]}}';

if ($simulated_http_code >= 200 && $simulated_http_code < 300) {
    $response['status'] = 'success';
    $response['message'] = 'SMS sent successfully! (Simulated)';
} else {
    $response['message'] = 'Failed to send SMS. (Simulated)';
    // $response['details'] = json_decode($api_response); // For debugging
}


echo json_encode($response);
?>

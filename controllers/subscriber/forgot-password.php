<?php
// Use absolute paths relative to the project root
require_once __DIR__ . '/../../Database.php';
require_once __DIR__ . '/../../models/SubscriberAuth.php';
require_once __DIR__ . '/../../functions.php';

$config = require __DIR__ . '/../../config.php';

// Initialize database
$db = new Database($config['database']);

// Initialize auth service
$subscriberAuth = new SubscriberAuth($db);

// Define variables
$error = '';
$success = '';
$accountNoOrEmail = '';

// Check if subscriber is already logged in
if ($subscriberAuth->isLoggedIn()) {
    header('Location: ' . url('subscriber/dashboard'));
    exit;
}

// Process forgot password form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accountNoOrEmail = $_POST['account_no_or_email'] ?? '';

    // Validate input
    if (empty($accountNoOrEmail)) {
        $error = 'Please enter your account number or email';
    } else {
        // Create password reset token
        $result = $subscriberAuth->createPasswordResetToken($accountNoOrEmail);
        
        if ($result) {
            // In a real-world application, you would send an email with the reset link
            // For now, we'll just set a success message with the reset URL included
            
            $resetLink = url("subscriber/reset-password?id={$result['subscriber_id']}&token={$result['token']}");
            
            // Set success message
            $success = 'A password reset link has been generated. In a production environment, this would be emailed to you.';
            
            // For testing/demo purposes only, we'll show the reset link directly
            // In a real application, you would NOT expose this to the user like this
            $success .= '<br><br><strong>Reset Link (for testing only):</strong><br>';
            $success .= '<a href="' . $resetLink . '">' . $resetLink . '</a>';
            
            // Clear the input
            $accountNoOrEmail = '';
        } else {
            $error = 'No account found with that account number or email';
        }
    }
}

// Load view
require __DIR__ . '/../../views/subscriber/forgot-password.view.php';
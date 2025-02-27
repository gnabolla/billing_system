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
$validToken = false;

// Check if subscriber is already logged in
if ($subscriberAuth->isLoggedIn()) {
    header('Location: ' . url('subscriber/dashboard'));
    exit;
}

// Get subscriber ID and token from query parameters
$subscriberId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$token = isset($_GET['token']) ? $_GET['token'] : '';

// Validate token
if ($subscriberId > 0 && !empty($token)) {
    $validToken = $subscriberAuth->verifyPasswordResetToken($subscriberId, $token);
    
    if (!$validToken) {
        $error = 'Invalid or expired password reset link. Please request a new one.';
    }
} else {
    $error = 'Missing required parameters. Please request a new password reset link.';
}

// Process reset password form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validToken) {
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Validate input
    if (empty($password)) {
        $error = 'Please enter a new password';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match';
    } else {
        // Reset password
        $result = $subscriberAuth->resetPassword($subscriberId, $token, $password);
        
        if ($result) {
            // Set success message
            $success = 'Your password has been reset successfully. You can now login with your new password.';
            
            // Redirect to login page after a short delay
            header('Refresh: 3; URL=' . url('subscriber/login'));
        } else {
            $error = 'Failed to reset password. Please try again or request a new reset link.';
        }
    }
}

// Load view
require __DIR__ . '/../../views/subscriber/reset-password.view.php';
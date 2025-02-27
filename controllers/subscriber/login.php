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
$accountNo = '';

// Check if subscriber is already logged in
if ($subscriberAuth->isLoggedIn()) {
    header('Location: ' . url('subscriber/dashboard'));
    exit;
}

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accountNo = $_POST['account_no'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validate input
    if (empty($accountNo) || empty($password)) {
        $error = 'Please enter both account number and password';
    } else {
        // Attempt to log in
        if ($subscriberAuth->login($accountNo, $password)) {
            // Redirect to dashboard on success
            header('Location: ' . url('subscriber/dashboard'));
            exit;
        } else {
            $error = 'Invalid account number or password';
        }
    }
}

// Load view
require __DIR__ . '/../../views/subscriber/login.view.php';
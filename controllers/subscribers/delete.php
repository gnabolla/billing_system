<?php

// Use absolute paths relative to the project root
require_once __DIR__ . '/../../Database.php';
require_once __DIR__ . '/../../services/Auth.php';
require_once __DIR__ . '/../../models/Subscriber.php';
require_once __DIR__ . '/../../functions.php';

$config = require __DIR__ . '/../../config.php';

// Initialize database
$db = new Database($config['database']);

// Initialize auth service
$auth = new Auth($db);

// Check if user is logged in
if (!$auth->isLoggedIn()) {
    header('Location: ' . url('login'));
    exit;
}

// Get company ID from session
$companyId = $_SESSION['company_id'];

// Initialize subscriber model
$subscriberModel = new Subscriber($db);

// Get subscriber ID from URL
$subscriberId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Validate subscriber ID
if ($subscriberId <= 0) {
    $_SESSION['flash_message'] = 'Invalid subscriber ID';
    $_SESSION['flash_message_type'] = 'error';
    header('Location: ' . url('subscribers'));
    exit;
}

// Verify this is a POST request (for security)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['flash_message'] = 'Invalid request method';
    $_SESSION['flash_message_type'] = 'error';
    header('Location: ' . url('subscribers'));
    exit;
}

// Delete subscriber
$deleted = $subscriberModel->delete($subscriberId, $companyId);

if ($deleted) {
    $_SESSION['flash_message'] = 'Subscriber deleted successfully';
    $_SESSION['flash_message_type'] = 'success';
} else {
    $_SESSION['flash_message'] = 'Failed to delete subscriber';
    $_SESSION['flash_message_type'] = 'error';
}

// Redirect back to subscriber list
header('Location: ' . url('subscribers'));
exit;
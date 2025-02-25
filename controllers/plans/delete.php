<?php

// Use absolute paths relative to the project root
require_once __DIR__ . '/../../Database.php';
require_once __DIR__ . '/../../services/Auth.php';
require_once __DIR__ . '/../../models/Plan.php';
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

// Initialize plan model
$planModel = new Plan($db);

// Get plan ID from URL
$planId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Validate plan ID
if ($planId <= 0) {
    $_SESSION['flash_message'] = 'Invalid plan ID';
    $_SESSION['flash_message_type'] = 'error';
    header('Location: ' . url('plans'));
    exit;
}

// Verify this is a POST request (for security)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['flash_message'] = 'Invalid request method';
    $_SESSION['flash_message_type'] = 'error';
    header('Location: ' . url('plans'));
    exit;
}

// Delete plan
$deleted = $planModel->delete($planId, $companyId);

if ($deleted) {
    $_SESSION['flash_message'] = 'Plan deleted successfully';
    $_SESSION['flash_message_type'] = 'success';
} else {
    $_SESSION['flash_message'] = 'Failed to delete plan. It may be in use by subscribers.';
    $_SESSION['flash_message_type'] = 'error';
}

// Redirect back to plan list
header('Location: ' . url('plans'));
exit;
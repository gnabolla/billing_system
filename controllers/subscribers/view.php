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

// Get subscriber data
$subscriber = $subscriberModel->getById($subscriberId, $companyId);

// Check if subscriber exists and belongs to this company
if (!$subscriber) {
    $_SESSION['flash_message'] = 'Subscriber not found';
    $_SESSION['flash_message_type'] = 'error';
    header('Location: ' . url('subscribers'));
    exit;
}

// TODO: Get subscriber plans, statements, and payments
$subscriberPlans = []; // This would come from a SubscriberPlan model
$statements = []; // This would come from a Statement model
$payments = []; // This would come from a Payment model

// Load view
require __DIR__ . '/../../views/subscribers/view.view.php';
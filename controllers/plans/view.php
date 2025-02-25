<?php

// Use absolute paths relative to the project root
require_once __DIR__ . '/../../Database.php';
require_once __DIR__ . '/../../services/Auth.php';
require_once __DIR__ . '/../../models/Plan.php';
require_once __DIR__ . '/../../models/SubscriberPlan.php';
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

// Initialize models
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

// Get plan data
$plan = $planModel->getById($planId, $companyId);

// Check if plan exists and belongs to this company
if (!$plan) {
    $_SESSION['flash_message'] = 'Plan not found';
    $_SESSION['flash_message_type'] = 'error';
    header('Location: ' . url('plans'));
    exit;
}

// Get subscribers using this plan (implemented later)
$subscribersUsingPlan = []; // Placeholder for now

// Load view
require __DIR__ . '/../../views/plans/view.view.php';
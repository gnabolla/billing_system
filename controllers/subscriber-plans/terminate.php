<?php

// Use absolute paths relative to the project root
require_once __DIR__ . '/../../Database.php';
require_once __DIR__ . '/../../services/Auth.php';
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
$subscriberPlanModel = new SubscriberPlan($db);

// Get subscriber plan ID from URL
$subscriberPlanId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Validate subscriber plan ID
if ($subscriberPlanId <= 0) {
    $_SESSION['flash_message'] = 'Invalid subscriber plan ID';
    $_SESSION['flash_message_type'] = 'error';
    header('Location: ' . url('subscribers'));
    exit;
}

// Get subscriber plan data
$subscriberPlan = $subscriberPlanModel->getById($subscriberPlanId, $companyId);

// Check if subscriber plan exists and belongs to this company
if (!$subscriberPlan) {
    $_SESSION['flash_message'] = 'Subscriber plan not found';
    $_SESSION['flash_message_type'] = 'error';
    header('Location: ' . url('subscribers'));
    exit;
}

// Initialize variables
$errors = [];
$subscriberId = $subscriberPlan['subscriber_id'];
$endDate = date('Y-m-d'); // Default to today

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get end date from form
    $endDate = $_POST['end_date'] ?? date('Y-m-d');
    
    // Validate end date
    if (empty($endDate)) {
        $errors['end_date'] = 'End date is required';
    } else {
        $startDate = new DateTime($subscriberPlan['start_date']);
        $terminationDate = new DateTime($endDate);
        
        if ($terminationDate < $startDate) {
            $errors['end_date'] = 'End date cannot be before start date';
        }
    }
    
    // If no errors, terminate the plan
    if (empty($errors)) {
        $terminated = $subscriberPlanModel->terminate($subscriberPlanId, $endDate, $companyId);
        
        if ($terminated) {
            // Set success message in session
            $_SESSION['flash_message'] = 'Plan terminated successfully';
            $_SESSION['flash_message_type'] = 'success';
            
            // Redirect to subscriber view page
            header('Location: ' . url('subscribers/view?id=' . $subscriberId));
            exit;
        } else {
            $errors['general'] = 'Failed to terminate plan. Please try again.';
        }
    }
}

// Load view
require __DIR__ . '/../../views/subscriber-plans/terminate.view.php';
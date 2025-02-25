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

// Initialize variables
$errors = [];
$formData = [
    'plan_name' => '',
    'plan_description' => '',
    'monthly_fee' => '',
    'speed_rate' => '',
    'billing_cycle' => 'Monthly',
    'status' => 'Active'
];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $formData = [
        'company_id' => $companyId,
        'plan_name' => $_POST['plan_name'] ?? '',
        'plan_description' => $_POST['plan_description'] ?? '',
        'monthly_fee' => $_POST['monthly_fee'] ?? '',
        'speed_rate' => $_POST['speed_rate'] ?? '',
        'billing_cycle' => $_POST['billing_cycle'] ?? 'Monthly',
        'status' => $_POST['status'] ?? 'Active'
    ];
    
    // Validate required fields
    if (empty($formData['plan_name'])) {
        $errors['plan_name'] = 'Plan name is required';
    }
    
    if (empty($formData['monthly_fee'])) {
        $errors['monthly_fee'] = 'Monthly fee is required';
    } elseif (!is_numeric($formData['monthly_fee']) || floatval($formData['monthly_fee']) < 0) {
        $errors['monthly_fee'] = 'Monthly fee must be a valid positive number';
    }
    
    // If no errors, create plan
    if (empty($errors)) {
        // Convert monthly_fee to decimal value
        $formData['monthly_fee'] = number_format((float)$formData['monthly_fee'], 2, '.', '');
        
        $planId = $planModel->create($formData);
        
        if ($planId) {
            // Set success message in session
            $_SESSION['flash_message'] = 'Plan created successfully';
            $_SESSION['flash_message_type'] = 'success';
            
            // Redirect to plan list
            header('Location: ' . url('plans'));
            exit;
        } else {
            $errors['general'] = 'Failed to create plan. Please try again.';
        }
    }
}

// Load view
require __DIR__ . '/../../views/plans/create.view.php';
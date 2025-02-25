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

// Get plan data
$plan = $planModel->getById($planId, $companyId);

// Check if plan exists and belongs to this company
if (!$plan) {
    $_SESSION['flash_message'] = 'Plan not found';
    $_SESSION['flash_message_type'] = 'error';
    header('Location: ' . url('plans'));
    exit;
}

// Initialize variables
$errors = [];
$formData = $plan;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $formData = [
        'plan_name' => $_POST['plan_name'] ?? $plan['plan_name'],
        'plan_description' => $_POST['plan_description'] ?? $plan['plan_description'],
        'monthly_fee' => $_POST['monthly_fee'] ?? $plan['monthly_fee'],
        'speed_rate' => $_POST['speed_rate'] ?? $plan['speed_rate'],
        'billing_cycle' => $_POST['billing_cycle'] ?? $plan['billing_cycle'],
        'status' => $_POST['status'] ?? $plan['status']
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
    
    // If no errors, update plan
    if (empty($errors)) {
        // Convert monthly_fee to decimal value
        $formData['monthly_fee'] = number_format((float)$formData['monthly_fee'], 2, '.', '');
        
        $updated = $planModel->update($planId, $formData, $companyId);
        
        if ($updated) {
            // Set success message in session
            $_SESSION['flash_message'] = 'Plan updated successfully';
            $_SESSION['flash_message_type'] = 'success';
            
            // Redirect to plan list
            header('Location: ' . url('plans'));
            exit;
        } else {
            $errors['general'] = 'Failed to update plan. Please try again.';
        }
    }
}

// Load view
require __DIR__ . '/../../views/plans/edit.view.php';
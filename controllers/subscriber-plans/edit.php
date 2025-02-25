<?php

// Use absolute paths relative to the project root
require_once __DIR__ . '/../../Database.php';
require_once __DIR__ . '/../../services/Auth.php';
require_once __DIR__ . '/../../models/Subscriber.php';
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
$subscriberPlanModel = new SubscriberPlan($db);
$planModel = new Plan($db);

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

// Get all active plans for this company
$filters = ['status' => 'Active'];
$plans = $planModel->getAllByCompany($companyId, $filters);

// Initialize variables
$errors = [];
$formData = $subscriberPlan;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $formData = [
        'plan_id' => $_POST['plan_id'] ?? $subscriberPlan['plan_id'],
        'start_date' => $_POST['start_date'] ?? $subscriberPlan['start_date'],
        'end_date' => $_POST['end_date'] ?? $subscriberPlan['end_date'],
        'status' => $_POST['status'] ?? $subscriberPlan['status'],
        'notes' => $_POST['notes'] ?? $subscriberPlan['notes']
    ];
    
    // Validate required fields
    if (empty($formData['plan_id'])) {
        $errors['plan_id'] = 'Plan is required';
    }
    
    if (empty($formData['start_date'])) {
        $errors['start_date'] = 'Start date is required';
    }
    
    // If end date is provided, make sure it's after start date
    if (!empty($formData['end_date'])) {
        $startDate = new DateTime($formData['start_date']);
        $endDate = new DateTime($formData['end_date']);
        
        if ($endDate <= $startDate) {
            $errors['end_date'] = 'End date must be after start date';
        }
    }
    
    // If status is not Active, ensure an end date is provided
    if ($formData['status'] !== 'Active' && empty($formData['end_date'])) {
        $errors['end_date'] = 'End date is required for non-active status';
    }
    
    // If no errors, update subscriber plan
    if (empty($errors)) {
        $updated = $subscriberPlanModel->update($subscriberPlanId, $formData, $companyId);
        
        if ($updated) {
            // Set success message in session
            $_SESSION['flash_message'] = 'Subscriber plan updated successfully';
            $_SESSION['flash_message_type'] = 'success';
            
            // Redirect to subscriber view page
            header('Location: ' . url('subscribers/view?id=' . $subscriberPlan['subscriber_id']));
            exit;
        } else {
            $errors['general'] = 'Failed to update subscriber plan. Please try again.';
        }
    }
}

// Load view
require __DIR__ . '/../../views/subscriber-plans/edit.view.php';
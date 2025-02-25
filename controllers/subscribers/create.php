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

// Initialize variables
$errors = [];
$formData = [
    'account_no' => '',
    'company_name' => '',
    'last_name' => '',
    'first_name' => '',
    'middle_name' => '',
    'address' => '',
    'phone_number' => '',
    'email' => '',
    'registration_date' => date('Y-m-d'),
    'status' => 'Active'
];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $formData = [
        'company_id' => $companyId,
        'account_no' => $_POST['account_no'] ?? '',
        'company_name' => $_POST['company_name'] ?? '',
        'last_name' => $_POST['last_name'] ?? '',
        'first_name' => $_POST['first_name'] ?? '',
        'middle_name' => $_POST['middle_name'] ?? '',
        'address' => $_POST['address'] ?? '',
        'phone_number' => $_POST['phone_number'] ?? '',
        'email' => $_POST['email'] ?? '',
        'registration_date' => $_POST['registration_date'] ?? date('Y-m-d'),
        'status' => $_POST['status'] ?? 'Active'
    ];
    
    // Validate account number
    if (empty($formData['account_no'])) {
        // Generate account number if not provided
        $formData['account_no'] = $subscriberModel->generateAccountNumber($companyId);
    } else {
        // Check if account number already exists
        $existingSubscriber = $subscriberModel->getByAccountNo($formData['account_no'], $companyId);
        if ($existingSubscriber) {
            $errors['account_no'] = 'Account number already exists';
        }
    }
    
    // Validate required fields
    if (empty($formData['last_name']) && empty($formData['company_name'])) {
        $errors['last_name'] = 'Either Last Name or Company Name is required';
        $errors['company_name'] = 'Either Last Name or Company Name is required';
    }
    
    if (empty($formData['first_name']) && empty($formData['company_name'])) {
        $errors['first_name'] = 'Either First Name or Company Name is required';
    }
    
    if (empty($formData['address'])) {
        $errors['address'] = 'Address is required';
    }
    
    // Validate email format
    if (!empty($formData['email']) && !filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }
    
    // If no errors, create subscriber
    if (empty($errors)) {
        $subscriberId = $subscriberModel->create($formData);
        
        if ($subscriberId) {
            // Set success message in session
            $_SESSION['flash_message'] = 'Subscriber created successfully';
            $_SESSION['flash_message_type'] = 'success';
            
            // Redirect to subscriber list
            header('Location: ' . url('subscribers'));
            exit;
        } else {
            $errors['general'] = 'Failed to create subscriber. Please try again.';
        }
    }
}

// Load view
require __DIR__ . '/../../views/subscribers/create.view.php';
<?php

// Use absolute paths relative to the project root
require_once __DIR__ . '/../Database.php';
require_once __DIR__ . '/../services/Auth.php';
require_once __DIR__ . '/../models/Company.php';
require_once __DIR__ . '/../functions.php';

$config = require __DIR__ . '/../config.php';

// Initialize database
$db = new Database($config['database']);

// Initialize auth service
$auth = new Auth($db);

// Check if user is logged in
if (!$auth->isLoggedIn()) {
    header('Location: ' . url('login'));
    exit;
}

// Check if user is admin
if ($_SESSION['role'] !== 'admin') {
    $_SESSION['flash_message'] = 'You do not have permission to access company settings.';
    $_SESSION['flash_message_type'] = 'danger';
    header('Location: ' . url('dashboard'));
    exit;
}

// Initialize company model
$companyModel = new Company($db);

// Get company data
$company = $companyModel->getById($_SESSION['company_id']);

// Initialize variables
$errors = [];
$formData = $company;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $formData = [
        'company_name' => $_POST['company_name'] ?? $company['company_name'],
        'contact_person' => $_POST['contact_person'] ?? $company['contact_person'],
        'contact_email' => $_POST['contact_email'] ?? $company['contact_email'],
        'contact_phone' => $_POST['contact_phone'] ?? $company['contact_phone'],
        'address' => $_POST['address'] ?? $company['address'],
    ];
    
    // Validate required fields
    if (empty($formData['company_name'])) {
        $errors['company_name'] = 'Company name is required';
    }
    
    if (empty($formData['contact_email'])) {
        $errors['contact_email'] = 'Contact email is required';
    } elseif (!filter_var($formData['contact_email'], FILTER_VALIDATE_EMAIL)) {
        $errors['contact_email'] = 'Invalid email format';
    }
    
    // If no errors, update company
    if (empty($errors)) {
        $updated = $companyModel->update($_SESSION['company_id'], $formData);
        
        if ($updated) {
            // Set success message in session
            $_SESSION['flash_message'] = 'Company settings updated successfully';
            $_SESSION['flash_message_type'] = 'success';
            
            // Redirect to prevent form resubmission
            header('Location: ' . url('company-settings'));
            exit;
        } else {
            $errors['general'] = 'Failed to update company settings. Please try again.';
        }
    }
}

// Load view
require __DIR__ . '/../views/company-settings.view.php';
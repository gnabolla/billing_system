<?php

// Use absolute paths relative to the project root
require_once __DIR__ . '/../Database.php';
require_once __DIR__ . '/../services/Auth.php';
require_once __DIR__ . '/../functions.php';

$config = require __DIR__ . '/../config.php';

// Initialize database
$db = new Database($config['database']);

// Initialize auth service
$auth = new Auth($db);

// Define variables
$errors = [];
$formData = [
    'company_name' => '',
    'contact_person' => '',
    'contact_email' => '',
    'contact_phone' => '',
    'username' => '',
    'email' => '',
    'first_name' => '',
    'last_name' => ''
];

// Check if user is already logged in
if ($auth->isLoggedIn()) {
    header('Location: ' . url('dashboard'));
    exit;
}

// Process registration form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $formData = [
        'company_name' => $_POST['company_name'] ?? '',
        'contact_person' => $_POST['contact_person'] ?? '',
        'contact_email' => $_POST['contact_email'] ?? '',
        'contact_phone' => $_POST['contact_phone'] ?? '',
        'username' => $_POST['username'] ?? '',
        'email' => $_POST['email'] ?? '',
        'first_name' => $_POST['first_name'] ?? '',
        'last_name' => $_POST['last_name'] ?? ''
    ];
    
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate company data
    if (empty($formData['company_name'])) {
        $errors['company_name'] = 'Company name is required';
    }

    if (empty($formData['contact_email'])) {
        $errors['contact_email'] = 'Contact email is required';
    } elseif (!filter_var($formData['contact_email'], FILTER_VALIDATE_EMAIL)) {
        $errors['contact_email'] = 'Invalid contact email format';
    }

    // Validate user data
    if (empty($formData['username'])) {
        $errors['username'] = 'Username is required';
    } elseif (strlen($formData['username']) < 4) {
        $errors['username'] = 'Username must be at least 4 characters';
    }

    if (empty($formData['email'])) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }

    if (empty($password)) {
        $errors['password'] = 'Password is required';
    } elseif (strlen($password) < 8) {
        $errors['password'] = 'Password must be at least 8 characters';
    }

    if ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Passwords do not match';
    }

    if (empty($formData['first_name'])) {
        $errors['first_name'] = 'First name is required';
    }

    if (empty($formData['last_name'])) {
        $errors['last_name'] = 'Last name is required';
    }

    // If no errors, process registration
    if (empty($errors)) {
        // Prepare company data
        $companyData = [
            'company_name' => $formData['company_name'],
            'contact_person' => $formData['contact_person'],
            'contact_email' => $formData['contact_email'],
            'contact_phone' => $formData['contact_phone'],
            'subscription_status' => 'Active',
            'subscription_plan' => 'Basic'
        ];

        // Prepare user data
        $userData = [
            'username' => $formData['username'],
            'email' => $formData['email'],
            'password' => $password,
            'first_name' => $formData['first_name'],
            'last_name' => $formData['last_name'],
            'status' => 'Active'
        ];

        // Attempt to register
        $result = $auth->registerCompany($companyData, $userData);

        if ($result) {
            // Auto-login the user
            $auth->login($formData['username'], $password);
            
            // Redirect to dashboard
            header('Location: ' . url('dashboard'));
            exit;
        } else {
            $errors['general'] = 'Registration failed. Please try again.';
        }
    }
}

// Load view
require __DIR__ . '/../views/register.view.php';
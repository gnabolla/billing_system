<?php

// Use absolute paths relative to the project root
require_once __DIR__ . '/../../Database.php';
require_once __DIR__ . '/../../services/Auth.php';
require_once __DIR__ . '/../../models/Statement.php';
require_once __DIR__ . '/../../models/Payment.php';
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
$userId = $_SESSION['user_id'];

// Initialize models
$statementModel = new Statement($db);
$paymentModel = new Payment($db);
$subscriberModel = new Subscriber($db);

// Get statement ID from URL if provided
$statementId = isset($_GET['statement_id']) ? (int)$_GET['statement_id'] : 0;
$statement = null;
$subscriber = null;

if ($statementId > 0) {
    $statement = $statementModel->getById($statementId, $companyId);
    if ($statement) {
        $subscriber = $subscriberModel->getById($statement['subscriber_id'], $companyId);
    }
}

// Get all unpaid statements if no specific statement is selected
$unpaidStatements = [];
if (!$statementId) {
    // Get statements with unpaid amounts
    $filters = ['status' => 'Unpaid'];
    // This would need an additional method in the Statement model to get only unpaid statements
    // For now, just get all unpaid statements
    $unpaidStatements = $statementModel->getAllByCompany($companyId, $filters, 100, 0);
}

// Initialize variables
$errors = [];
$formData = [
    'statement_id' => $statementId,
    'or_no' => '',
    'or_date' => date('Y-m-d'),
    'paid_amount' => $statement ? $statement['unpaid_amount'] : 0,
    'adv_payment' => 0,
    'payment_method' => 'Cash',
    'payment_date' => date('Y-m-d'),
    'payment_status' => 'Completed',
    'notes' => ''
];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $formData = [
        'company_id' => $companyId,
        'statement_id' => $_POST['statement_id'] ?? '',
        'or_no' => $_POST['or_no'] ?? '',
        'or_date' => $_POST['or_date'] ?? date('Y-m-d'),
        'paid_amount' => $_POST['paid_amount'] ?? 0,
        'adv_payment' => $_POST['adv_payment'] ?? 0,
        'payment_method' => $_POST['payment_method'] ?? 'Cash',
        'payment_date' => $_POST['payment_date'] ?? date('Y-m-d'),
        'payment_status' => 'Completed',
        'notes' => $_POST['notes'] ?? '',
        'created_by' => $userId
    ];
    
    // Validate required fields
    if (empty($formData['statement_id'])) {
        $errors['statement_id'] = 'Statement is required';
    }
    
    if (empty($formData['paid_amount']) || $formData['paid_amount'] <= 0) {
        $errors['paid_amount'] = 'Paid amount is required and must be greater than zero';
    }
    
    if (empty($formData['payment_date'])) {
        $errors['payment_date'] = 'Payment date is required';
    }
    
    if (empty($formData['payment_method'])) {
        $errors['payment_method'] = 'Payment method is required';
    }
    
    // Validate payment amount against statement balance
    if (!empty($formData['statement_id']) && !empty($formData['paid_amount'])) {
        $selectedStatement = $statementModel->getById($formData['statement_id'], $companyId);
        if ($selectedStatement && $formData['paid_amount'] > $selectedStatement['unpaid_amount']) {
            $errors['paid_amount'] = 'Paid amount cannot exceed the unpaid balance of $' . 
                                     number_format($selectedStatement['unpaid_amount'], 2);
        }
    }
    
    // If no errors, create payment
    if (empty($errors)) {
        $paymentId = $paymentModel->create($formData);
        
        if ($paymentId) {
            // Set success message in session
            $_SESSION['flash_message'] = 'Payment recorded successfully';
            $_SESSION['flash_message_type'] = 'success';
            
            // Redirect to payment view
            header('Location: ' . url('payments/view?id=' . $paymentId));
            exit;
        } else {
            $errors['general'] = 'Failed to record payment. Please try again.';
        }
    }
}

// Load view
require __DIR__ . '/../../views/payments/create.view.php';
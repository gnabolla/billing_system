<?php

// Use absolute paths relative to the project root
require_once __DIR__ . '/../../Database.php';
require_once __DIR__ . '/../../services/Auth.php';
require_once __DIR__ . '/../../models/Subscriber.php';
require_once __DIR__ . '/../../models/Statement.php';
require_once __DIR__ . '/../../models/SubscriberPlan.php';
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

// Initialize models
$subscriberModel = new Subscriber($db);
$statementModel = new Statement($db);
$subscriberPlanModel = new SubscriberPlan($db);
$planModel = new Plan($db);

// Get subscriber ID from URL if provided
$subscriberId = isset($_GET['subscriber_id']) ? (int)$_GET['subscriber_id'] : 0;
$subscriber = null;
$subscriberPlans = [];

if ($subscriberId > 0) {
    $subscriber = $subscriberModel->getById($subscriberId, $companyId);
    if ($subscriber) {
        $subscriberPlans = $subscriberPlanModel->getActiveForSubscriber($subscriberId, $companyId);
    }
}

// Get all subscribers if not filtered
$subscribers = [];
if (!$subscriberId) {
    $filters = ['status' => 'Active'];
    $subscribers = $subscriberModel->getAllByCompany($companyId, $filters, 100, 0);
}

// Initialize variables
$errors = [];
$formData = [
    'subscriber_id' => $subscriberId,
    'statement_no' => '',
    'bill_period_start' => date('Y-m-d', strtotime('first day of this month')),
    'bill_period_end' => date('Y-m-d', strtotime('last day of this month')),
    'amount' => 0,
    'tax_amount' => 0,
    'discount_amount' => 0,
    'total_amount' => 0,
    'unpaid_amount' => 0,
    'due_date' => date('Y-m-d', strtotime('+15 days')),
    'status' => 'Unpaid',
    'notes' => '',
    'items' => []
];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $formData = [
        'company_id' => $companyId,
        'subscriber_id' => $_POST['subscriber_id'] ?? '',
        'statement_no' => $_POST['statement_no'] ?? '',
        'bill_period_start' => $_POST['bill_period_start'] ?? '',
        'bill_period_end' => $_POST['bill_period_end'] ?? '',
        'amount' => 0, // Will be calculated from items
        'tax_amount' => $_POST['tax_amount'] ?? 0,
        'discount_amount' => $_POST['discount_amount'] ?? 0,
        'total_amount' => 0, // Will be calculated
        'unpaid_amount' => 0, // Will be calculated
        'due_date' => $_POST['due_date'] ?? '',
        'status' => 'Unpaid',
        'notes' => $_POST['notes'] ?? '',
        'items' => []
    ];
    
    // Validate required fields
    if (empty($formData['subscriber_id'])) {
        $errors['subscriber_id'] = 'Subscriber is required';
    }
    
    if (empty($formData['bill_period_start'])) {
        $errors['bill_period_start'] = 'Billing period start date is required';
    }
    
    if (empty($formData['bill_period_end'])) {
        $errors['bill_period_end'] = 'Billing period end date is required';
    } else {
        // Check if end date is after start date
        $startDate = new DateTime($formData['bill_period_start']);
        $endDate = new DateTime($formData['bill_period_end']);
        
        if ($endDate < $startDate) {
            $errors['bill_period_end'] = 'End date must be after start date';
        }
    }
    
    if (empty($formData['due_date'])) {
        $errors['due_date'] = 'Due date is required';
    }
    
    // Get items from form
    $descriptions = $_POST['description'] ?? [];
    $amounts = $_POST['amount'] ?? [];
    $taxRates = $_POST['tax_rate'] ?? [];
    $taxAmounts = $_POST['tax_amount_item'] ?? [];
    $discountAmounts = $_POST['discount_amount_item'] ?? [];
    $totalAmounts = $_POST['total_amount_item'] ?? [];
    
    $itemCount = count($descriptions);
    $totalAmount = 0;
    $totalTax = 0;
    $totalDiscount = 0;
    
    for ($i = 0; $i < $itemCount; $i++) {
        if (!empty($descriptions[$i]) && isset($amounts[$i])) {
            $amount = floatval($amounts[$i]);
            $taxRate = floatval($taxRates[$i] ?? 0);
            $taxAmount = floatval($taxAmounts[$i] ?? 0);
            $discountAmount = floatval($discountAmounts[$i] ?? 0);
            $itemTotalAmount = floatval($totalAmounts[$i] ?? $amount);
            
            $formData['items'][] = [
                'description' => $descriptions[$i],
                'amount' => $amount,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $itemTotalAmount
            ];
            
            $totalAmount += $amount;
            $totalTax += $taxAmount;
            $totalDiscount += $discountAmount;
        }
    }
    
    if (empty($formData['items'])) {
        $errors['items'] = 'At least one item is required';
    }
    
    // Calculate totals
    $formData['amount'] = $totalAmount;
    $formData['tax_amount'] = $totalTax;
    $formData['discount_amount'] = $totalDiscount;
    $formData['total_amount'] = $totalAmount + $totalTax - $totalDiscount;
    $formData['unpaid_amount'] = $formData['total_amount']; // Initially, the full amount is unpaid
    
    // If no errors, create statement
    if (empty($errors)) {
        $statementId = $statementModel->create($formData);
        
        if ($statementId) {
            // Set success message in session
            $_SESSION['flash_message'] = 'Statement created successfully';
            $_SESSION['flash_message_type'] = 'success';
            
            // Redirect to statement view
            header('Location: ' . url('statements/view?id=' . $statementId));
            exit;
        } else {
            $errors['general'] = 'Failed to create statement. Please try again.';
        }
    }
}

// Load view
require __DIR__ . '/../../views/statements/create.view.php';
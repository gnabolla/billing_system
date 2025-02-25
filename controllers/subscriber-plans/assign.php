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
$subscriberModel = new Subscriber($db);
$planModel = new Plan($db);
$subscriberPlanModel = new SubscriberPlan($db);

// Get subscriber ID from URL
// Support both formats: ?subscriber_id=X and ?id=X
$subscriberId = isset($_GET['subscriber_id']) ? (int)$_GET['subscriber_id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);

// Validate subscriber ID
if ($subscriberId <= 0) {
    $_SESSION['flash_message'] = 'Invalid subscriber ID';
    $_SESSION['flash_message_type'] = 'error';
    header('Location: ' . url('subscribers'));
    exit;
}

// Get subscriber data
$subscriber = $subscriberModel->getById($subscriberId, $companyId);

// Check if subscriber exists and belongs to this company
if (!$subscriber) {
    $_SESSION['flash_message'] = 'Subscriber not found';
    $_SESSION['flash_message_type'] = 'error';
    header('Location: ' . url('subscribers'));
    exit;
}

// Get all active plans for this company
$filters = ['status' => 'Active'];
$plans = $planModel->getAllByCompany($companyId, $filters);

// Initialize variables
$errors = [];
$formData = [
    'subscriber_id' => $subscriberId,
    'plan_id' => '',
    'start_date' => date('Y-m-d'),
    'end_date' => '',
    'status' => 'Active',
    'notes' => ''
];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $formData = [
        'subscriber_id' => $subscriberId,
        'plan_id' => $_POST['plan_id'] ?? '',
        'start_date' => $_POST['start_date'] ?? date('Y-m-d'),
        'end_date' => $_POST['end_date'] ?? null,
        'status' => $_POST['status'] ?? 'Active',
        'notes' => $_POST['notes'] ?? ''
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

    // If no errors, assign plan to subscriber
    if (empty($errors)) {
        // Debug output for form data
        /*
        echo "Form data to be submitted:";
        echo "<pre>";
        print_r($formData);
        echo "</pre>";
        die();
        */

        $subscriberPlanId = $subscriberPlanModel->create($formData);

        if ($subscriberPlanId) {
            // Set success message in session
            $_SESSION['flash_message'] = 'Plan assigned successfully';
            $_SESSION['flash_message_type'] = 'success';

            // Redirect to subscriber view page
            header('Location: ' . url('subscribers/view?id=' . $subscriberId));
            exit;
        } else {
            $errors['general'] = 'Failed to assign plan. Please try again.';
        }
    }
}

// Load view
require __DIR__ . '/../../views/subscriber-plans/assign.view.php';

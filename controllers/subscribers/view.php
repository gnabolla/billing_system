<?php
// Use absolute paths relative to the project root
require_once __DIR__ . '/../../Database.php';
require_once __DIR__ . '/../../services/Auth.php';
require_once __DIR__ . '/../../models/Subscriber.php';
require_once __DIR__ . '/../../functions.php';
require_once __DIR__ . '/../../models/SubscriberPlan.php';

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

// Get subscriber ID from URL
$subscriberId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

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

// Get subscriber plans
$subscriberPlanModel = new SubscriberPlan($db);
$subscriberPlans = $subscriberPlanModel->getAllForSubscriber($subscriberId, $companyId);

// Direct database query for debugging subscriber plans
try {
    $sql = "SELECT sp.*, p.plan_name, p.monthly_fee
            FROM subscriber_plans sp
            JOIN plans p ON sp.plan_id = p.plan_id
            WHERE sp.subscriber_id = :subscriber_id
            ORDER BY sp.created_at DESC";
    
    $stmt = $db->query($sql, ['subscriber_id' => $subscriberId]);
    $directPlans = $stmt->fetchAll();
    
    if (empty($subscriberPlans) && !empty($directPlans)) {
        error_log("View debugging: Model returned empty results but direct query found plans:");
        error_log(print_r($directPlans, true));
        // Use the direct results as a fallback
        $subscriberPlans = $directPlans;
    }
} catch (Exception $e) {
    error_log("Error in direct plan query: " . $e->getMessage());
}

// Fetch recent statements for the subscriber
try {
    $sql = "SELECT * 
            FROM statements
            WHERE subscriber_id = :subscriber_id
            AND company_id = :company_id
            ORDER BY bill_period_end DESC
            LIMIT 5"; // Limit to 5 most recent statements

    $stmt = $db->query($sql, [
        'subscriber_id' => $subscriberId,
        'company_id' => $companyId
    ]);
    $statements = $stmt->fetchAll();

    // Debug logging
    if (empty($statements)) {
        error_log("No statements found for subscriber ID: $subscriberId");
    }
} catch (Exception $e) {
    error_log("Error fetching statements: " . $e->getMessage());
    $statements = [];
}

// Fetch recent payments (if needed)
$payments = [];
try {
    $sql = "SELECT p.*, s.statement_no
            FROM payments p
            JOIN statements s ON p.statement_id = s.statement_id
            WHERE s.subscriber_id = :subscriber_id
            AND s.company_id = :company_id
            ORDER BY p.payment_date DESC
            LIMIT 5"; // Limit to 5 most recent payments

    $stmt = $db->query($sql, [
        'subscriber_id' => $subscriberId,
        'company_id' => $companyId
    ]);
    $payments = $stmt->fetchAll();
} catch (Exception $e) {
    error_log("Error fetching payments: " . $e->getMessage());
    $payments = [];
}

// Uncomment for debugging
// echo "Subscriber ID: " . $subscriberId . "<br>";
// echo "Company ID: " . $companyId . "<br>";
// echo "Statements: <pre>" . print_r($statements, true) . "</pre>";
// die();

// Load view
require __DIR__ . '/../../views/subscribers/view.view.php';
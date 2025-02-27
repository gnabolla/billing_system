<?php

// Use absolute paths relative to the project root
require_once __DIR__ . '/../../Database.php';
require_once __DIR__ . '/../../models/SubscriberAuth.php';
require_once __DIR__ . '/../../models/Subscriber.php';
require_once __DIR__ . '/../../models/SubscriberPlan.php';
require_once __DIR__ . '/../../models/Statement.php';
require_once __DIR__ . '/../../models/Payment.php';
require_once __DIR__ . '/../../functions.php';

$config = require __DIR__ . '/../../config.php';

// Initialize database
$db = new Database($config['database']);

// Initialize auth service
$subscriberAuth = new SubscriberAuth($db);

// Check if subscriber is logged in
if (!$subscriberAuth->isLoggedIn()) {
    header('Location: ' . url('subscriber/login'));
    exit;
}

// Get current subscriber data
$subscriber = $subscriberAuth->getCurrentSubscriber();
$subscriberId = $subscriber['subscriber_id'];
$companyId = $subscriber['company_id'];

// Initialize needed models
$subscriberModel = new Subscriber($db);
$subscriberPlanModel = new SubscriberPlan($db);
$statementModel = new Statement($db);
$paymentModel = new Payment($db);

// Get active subscription plans
$activePlans = $subscriberPlanModel->getActiveForSubscriber($subscriberId, $companyId);

// Get recent statements (limit to 5)
$recentStatements = $statementModel->getForSubscriber($subscriberId, $companyId, 5);

// Calculate billing stats
$totalUnpaid = 0;
$nextPaymentDue = null;

foreach ($recentStatements as $statement) {
    if ($statement['status'] !== 'Paid') {
        $totalUnpaid += $statement['unpaid_amount'];
        
        // Find the earliest due date that hasn't passed yet
        $dueDate = strtotime($statement['due_date']);
        if ($dueDate > time() && ($nextPaymentDue === null || $dueDate < $nextPaymentDue)) {
            $nextPaymentDue = $dueDate;
        }
    }
}

// Get recent payments (limit to 5)
$recentPayments = $paymentModel->getForSubscriber($subscriberId, $companyId, 5);

// Load view
require __DIR__ . '/../../views/subscriber/dashboard.view.php';
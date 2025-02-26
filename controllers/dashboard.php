<?php

// Use absolute paths relative to the project root
require_once __DIR__ . '/../Database.php';
require_once __DIR__ . '/../services/Auth.php';
require_once __DIR__ . '/../models/Subscriber.php';
require_once __DIR__ . '/../models/SubscriberPlan.php';
require_once __DIR__ . '/../models/Statement.php';
require_once __DIR__ . '/../models/Payment.php';
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

// Get current user and company data
$user = $auth->getCurrentUser();
$company = $auth->getCurrentCompany();

// Get subscriber count
$subscriberModel = new Subscriber($db);
$subscriberCount = $subscriberModel->countByCompany($_SESSION['company_id']);

// Get active plans count
$subscriberPlanModel = new SubscriberPlan($db);
$activePlansCount = $subscriberPlanModel->countActiveByCompany($_SESSION['company_id']);

// Get statements count
try {
    $sql = "SELECT COUNT(*) as statement_count 
            FROM statements 
            WHERE company_id = :company_id";
    
    $stmt = $db->query($sql, ['company_id' => $_SESSION['company_id']]);
    $result = $stmt->fetch();
    $statementsCount = $result['statement_count'] ?? 0;
} catch (Exception $e) {
    error_log("Error fetching statements count: " . $e->getMessage());
    $statementsCount = 0;
}

// Get recent statements
$statementModel = new Statement($db);
try {
    $sql = "SELECT s.*, 
            sub.account_no, sub.company_name, sub.first_name, sub.last_name
            FROM statements s
            JOIN subscribers sub ON s.subscriber_id = sub.subscriber_id
            WHERE s.company_id = :company_id 
            ORDER BY s.created_at DESC 
            LIMIT 5";
    
    $stmt = $db->query($sql, ['company_id' => $_SESSION['company_id']]);
    $recentStatements = $stmt->fetchAll();
} catch (Exception $e) {
    error_log("Error fetching recent statements: " . $e->getMessage());
    $recentStatements = [];
}

// Get recent payments
$paymentModel = new Payment($db);
try {
    $sql = "SELECT p.*, 
            s.statement_no, 
            sub.account_no, sub.company_name, sub.first_name, sub.last_name
            FROM payments p
            JOIN statements s ON p.statement_id = s.statement_id
            JOIN subscribers sub ON s.subscriber_id = sub.subscriber_id
            WHERE p.company_id = :company_id 
            ORDER BY p.payment_date DESC, p.created_at DESC 
            LIMIT 5";
    
    $stmt = $db->query($sql, ['company_id' => $_SESSION['company_id']]);
    $recentPayments = $stmt->fetchAll();
} catch (Exception $e) {
    error_log("Error fetching recent payments: " . $e->getMessage());
    $recentPayments = [];
}

// Load the dashboard view
require __DIR__ . '/../views/dashboard.view.php';
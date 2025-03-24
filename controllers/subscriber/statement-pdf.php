<?php

// Use absolute paths relative to the project root
require_once __DIR__ . '/../../Database.php';
require_once __DIR__ . '/../../models/SubscriberAuth.php';
require_once __DIR__ . '/../../models/Statement.php';
require_once __DIR__ . '/../../models/StatementItem.php';
require_once __DIR__ . '/../../models/Company.php';
require_once __DIR__ . '/../../models/Payment.php';
require_once __DIR__ . '/../../utils/StatementGenerator.php';
require_once __DIR__ . '/../../functions.php';

$config = require __DIR__ . '/../../config.php';

// Initialize database
$db = new Database($config['database']);

// Initialize subscriber auth service
$subscriberAuth = new SubscriberAuth($db);

// Check if subscriber is logged in
if (!$subscriberAuth->isLoggedIn()) {
    header('Location: ' . url('subscriber/login'));
    exit;
}

// Get subscriber and company IDs from session
$subscriberId = $_SESSION['subscriber_id'];
$companyId = $_SESSION['company_id'];

// Initialize models
$statementModel = new Statement($db);
$statementItemModel = new StatementItem($db);
$companyModel = new Company($db);
$paymentModel = new Payment($db);

// Get statement ID from URL
$statementId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Validate statement ID
if ($statementId <= 0) {
    $_SESSION['flash_message'] = 'Invalid statement ID';
    $_SESSION['flash_message_type'] = 'error';
    header('Location: ' . url('subscriber/statements'));
    exit;
}

// Get statement data
$statement = $statementModel->getById($statementId, $companyId);

// Check if statement exists, belongs to this company, and belongs to this subscriber
if (!$statement || $statement['subscriber_id'] != $subscriberId) {
    $_SESSION['flash_message'] = 'Statement not found or unauthorized';
    $_SESSION['flash_message_type'] = 'error';
    header('Location: ' . url('subscriber/statements'));
    exit;
}

// Get statement items
$statementItems = $statementItemModel->getAllForStatement($statementId);

// Get company details
$company = $companyModel->getById($companyId);

// Get payment history for this statement
$paymentHistory = $paymentModel->getForStatement($statementId, $companyId);

// Generate statement PDF
$statementPDF = new StatementGenerator($company, $statement, $statement, $statementItems, $paymentHistory);
$pdfContent = $statementPDF->generate();

// Set headers for PDF download
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="statement_' . $statement['statement_no'] . '.pdf"');
header('Cache-Control: max-age=0');

// Output PDF
echo $pdfContent;
exit;
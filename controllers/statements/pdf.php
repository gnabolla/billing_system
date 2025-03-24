<?php

// Use absolute paths relative to the project root
require_once __DIR__ . '/../../Database.php';
require_once __DIR__ . '/../../services/Auth.php';
require_once __DIR__ . '/../../models/Statement.php';
require_once __DIR__ . '/../../models/StatementItem.php';
require_once __DIR__ . '/../../models/Company.php';
require_once __DIR__ . '/../../models/Payment.php';
require_once __DIR__ . '/../../utils/StatementGenerator.php';
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
    header('Location: ' . url('statements'));
    exit;
}

// Get statement data
$statement = $statementModel->getById($statementId, $companyId);

// Check if statement exists and belongs to this company
if (!$statement) {
    $_SESSION['flash_message'] = 'Statement not found';
    $_SESSION['flash_message_type'] = 'error';
    header('Location: ' . url('statements'));
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
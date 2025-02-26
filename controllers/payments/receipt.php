<?php

// Use absolute paths relative to the project root
require_once __DIR__ . '/../../Database.php';
require_once __DIR__ . '/../../services/Auth.php';
require_once __DIR__ . '/../../models/Payment.php';
require_once __DIR__ . '/../../models/Statement.php';
require_once __DIR__ . '/../../models/StatementItem.php';
require_once __DIR__ . '/../../models/Company.php';
require_once __DIR__ . '/../../utils/ReceiptGenerator.php';
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
$paymentModel = new Payment($db);
$statementModel = new Statement($db);
$statementItemModel = new StatementItem($db);
$companyModel = new Company($db);

// Get payment ID from URL
$paymentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Validate payment ID
if ($paymentId <= 0) {
    $_SESSION['flash_message'] = 'Invalid payment ID';
    $_SESSION['flash_message_type'] = 'error';
    header('Location: ' . url('payments'));
    exit;
}

// Get payment data
$payment = $paymentModel->getById($paymentId, $companyId);

// Check if payment exists and belongs to this company
if (!$payment) {
    $_SESSION['flash_message'] = 'Payment not found';
    $_SESSION['flash_message_type'] = 'error';
    header('Location: ' . url('payments'));
    exit;
}

// Get statement details
$statement = $statementModel->getById($payment['statement_id'], $companyId);

// Get statement items
$statementItems = $statementItemModel->getAllForStatement($payment['statement_id']);

// Get company details
$company = $companyModel->getById($companyId);

// Generate receipt
$receipt = new ReceiptGenerator($company, $payment, $statement, $payment, $statementItems);
$pdfContent = $receipt->generate();

// Set headers for PDF download
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="receipt_' . $payment['or_no'] . '.pdf"');
header('Cache-Control: max-age=0');

// Output PDF
echo $pdfContent;
exit;
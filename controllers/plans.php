<?php

// Use absolute paths relative to the project root
require_once __DIR__ . '/../Database.php';
require_once __DIR__ . '/../services/Auth.php';
require_once __DIR__ . '/../models/Plan.php';
require_once __DIR__ . '/../models/SubscriberPlan.php';
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

// Get company ID from session
$companyId = $_SESSION['company_id'];

// Initialize plan model
$planModel = new Plan($db);
$subscriberPlanModel = new SubscriberPlan($db);

// Handle filters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$filters = [
    'status' => isset($_GET['status']) ? $_GET['status'] : '',
    'search' => isset($_GET['search']) ? $_GET['search'] : ''
];

// Get plans
$plans = $planModel->getAllByCompany($companyId, $filters, $limit, $offset);

// Get total count for pagination
$totalCount = $planModel->countByCompany($companyId, $filters);
$totalPages = ceil($totalCount / $limit);

// Get count of active subscriber plans
$activePlansCount = $subscriberPlanModel->countActiveByCompany($companyId);

// Load view
require __DIR__ . '/../views/plans/index.view.php';
<?php

// Use absolute paths relative to the project root
require_once __DIR__ . '/../Database.php';
require_once __DIR__ . '/../services/Auth.php';
require_once __DIR__ . '/../models/Subscriber.php';
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

// Initialize subscriber model
$subscriberModel = new Subscriber($db);

// Handle filters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$filters = [
    'status' => isset($_GET['status']) ? $_GET['status'] : '',
    'search' => isset($_GET['search']) ? $_GET['search'] : ''
];
// Get subscribers
$subscribers = $subscriberModel->getAllByCompany($companyId, $filters, $limit, $offset);

// Get total count for pagination
$totalCount = $subscriberModel->countByCompany($companyId, $filters);
$totalPages = ceil($totalCount / $limit);

// Load view
require __DIR__ . '/../views/subscribers/index.view.php';
<?php

// Use absolute paths relative to the project root
require_once __DIR__ . '/../Database.php';
require_once __DIR__ . '/../services/Auth.php';
require_once __DIR__ . '/../models/Subscriber.php';
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

// Get current user and company data
$user = $auth->getCurrentUser();
$company = $auth->getCurrentCompany();

// Get subscriber count
$subscriberModel = new Subscriber($db);
$subscriberCount = $subscriberModel->countByCompany($_SESSION['company_id']);


// Get subscriber count
$subscriberModel = new Subscriber($db);
$subscriberCount = $subscriberModel->countByCompany($_SESSION['company_id']);

// Get active plans count
$subscriberPlanModel = new SubscriberPlan($db);
$activePlansCount = $subscriberPlanModel->countActiveByCompany($_SESSION['company_id']);


// TODO: Get active plans count, statements count
$activePlansCount = 0;
$statementsCount = 0;
$statementsCount = 0;
// Load the dashboard view
require __DIR__ . '/../views/dashboard.view.php';

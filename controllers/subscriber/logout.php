<?php

// Use absolute paths relative to the project root
require_once __DIR__ . '/../../Database.php';
require_once __DIR__ . '/../../models/SubscriberAuth.php';
require_once __DIR__ . '/../../functions.php';

$config = require __DIR__ . '/../../config.php';

// Initialize database
$db = new Database($config['database']);

// Initialize subscriber auth service
$subscriberAuth = new SubscriberAuth($db);

// Logout the subscriber
$subscriberAuth->logout();

// Redirect to login page
header('Location: ' . url('subscriber/login'));
exit;
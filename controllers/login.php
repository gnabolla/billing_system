<?php
// Use absolute paths relative to the project root
require_once __DIR__ . '/../Database.php';
require_once __DIR__ . '/../services/Auth.php';
require_once __DIR__ . '/../functions.php';

$config = require __DIR__ . '/../config.php';

// Initialize database
$db = new Database($config['database']);

// Initialize auth service
$auth = new Auth($db);

// Define variables
$error = '';
$username = '';

// Check if user is already logged in
if ($auth->isLoggedIn()) {
    header('Location: ' . url('dashboard'));
    exit;
}

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validate input
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        // Attempt to log in
        if ($auth->login($username, $password)) {
            // Redirect to dashboard on success
            header('Location: ' . url('dashboard'));
            exit;
        } else {
            $error = 'Invalid username or password';
        }
    }
}

// Load view
require __DIR__ . '/../views/login.view.php';
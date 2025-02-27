<?php

// Script to set up the subscriber_auth table and initial records

// Use absolute paths relative to the project root
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/models/SubscriberAuth.php';
$config = require __DIR__ . '/config.php';

// Initialize database connection
$db = new Database($config['database']);

// Function to execute SQL
function executeSql($db, $sql, $message) {
    try {
        $db->query($sql);
        echo "✅ " . $message . "\n";
        return true;
    } catch (PDOException $e) {
        echo "❌ " . $message . " failed: " . $e->getMessage() . "\n";
        return false;
    }
}

// Create subscriber_auth table
echo "Creating subscriber_auth table...\n";
$sql = "
CREATE TABLE IF NOT EXISTS subscriber_auth (
    subscriber_auth_id       INT AUTO_INCREMENT PRIMARY KEY,
    subscriber_id            INT NOT NULL,
    password_hash            VARCHAR(255) NOT NULL,
    password_reset_token     VARCHAR(255) NULL,
    password_reset_expires   DATETIME NULL,
    last_login               DATETIME NULL,
    created_at               DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at               DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (subscriber_id) REFERENCES subscribers(subscriber_id),
    UNIQUE (subscriber_id)
)";
executeSql($db, $sql, "Created subscriber_auth table");

// Create indexes
echo "Creating indexes...\n";
$sql = "CREATE INDEX IF NOT EXISTS idx_subscriber_id ON subscriber_auth(subscriber_id)";
executeSql($db, $sql, "Created subscriber_id index");

$sql = "CREATE INDEX IF NOT EXISTS idx_password_reset_token ON subscriber_auth(password_reset_token)";
executeSql($db, $sql, "Created password_reset_token index");

// Initialize auth service
$subscriberAuth = new SubscriberAuth($db);

// Get all active subscribers that don't have auth records
$sql = "SELECT s.subscriber_id, s.account_no 
        FROM subscribers s 
        LEFT JOIN subscriber_auth sa ON s.subscriber_id = sa.subscriber_id
        WHERE s.status = 'Active' AND sa.subscriber_auth_id IS NULL";
$stmt = $db->query($sql);
$subscribers = $stmt->fetchAll();

// Create default auth records for active subscribers
echo "Creating default auth records for active subscribers...\n";
$count = 0;
foreach ($subscribers as $subscriber) {
    // Use account number as the default password
    $defaultPassword = $subscriber['account_no'];
    
    if ($subscriberAuth->register($subscriber['subscriber_id'], $defaultPassword)) {
        echo "  ✅ Created auth record for subscriber ID: {$subscriber['subscriber_id']} (Account: {$subscriber['account_no']})\n";
        $count++;
    } else {
        echo "  ❌ Failed to create auth record for subscriber ID: {$subscriber['subscriber_id']}\n";
    }
}

echo "\nCompleted subscriber auth setup: {$count} auth records created.\n";
echo "Default passwords are set to subscriber account numbers.\n";
echo "You should prompt subscribers to change their passwords on first login.\n";
<?php

// Script to initialize the database schema

// Use absolute paths relative to the project root
require_once __DIR__ . '/Database.php';
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

// Begin schema creation
echo "Creating database schema...\n";

// Create companies table
$sql = "CREATE TABLE IF NOT EXISTS companies (
    company_id          INT AUTO_INCREMENT PRIMARY KEY,
    company_name        VARCHAR(100) NOT NULL,
    contact_person      VARCHAR(100),
    contact_email       VARCHAR(100) NOT NULL,
    contact_phone       VARCHAR(20),
    address             VARCHAR(200),
    logo_url            VARCHAR(255),
    subscription_status VARCHAR(20) DEFAULT 'Active',
    subscription_plan   VARCHAR(50) DEFAULT 'Basic',
    created_at          DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
executeSql($db, $sql, "Created companies table");

// Create users table
$sql = "CREATE TABLE IF NOT EXISTS users (
    user_id             INT AUTO_INCREMENT PRIMARY KEY,
    company_id          INT NOT NULL,
    username            VARCHAR(50) NOT NULL,
    password_hash       VARCHAR(255) NOT NULL,
    email               VARCHAR(100) NOT NULL,
    first_name          VARCHAR(50),
    last_name           VARCHAR(50),
    role                VARCHAR(20) NOT NULL,
    status              VARCHAR(20) DEFAULT 'Active',
    last_login          DATETIME,
    created_at          DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(company_id),
    UNIQUE (email),
    UNIQUE (username)
)";
executeSql($db, $sql, "Created users table");

// Create subscribers table
$sql = "CREATE TABLE IF NOT EXISTS subscribers (
    subscriber_id       INT AUTO_INCREMENT PRIMARY KEY,
    company_id          INT NOT NULL,
    account_no          VARCHAR(50) NOT NULL,
    company_name        VARCHAR(100),
    last_name           VARCHAR(50),
    first_name          VARCHAR(50),
    middle_name         VARCHAR(50),
    address             VARCHAR(200),
    phone_number        VARCHAR(20),
    email               VARCHAR(100),
    registration_date   DATE,
    status              VARCHAR(20) DEFAULT 'Active',
    created_at          DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(company_id),
    UNIQUE (company_id, account_no)
)";
executeSql($db, $sql, "Created subscribers table");

// Create plans table
$sql = "CREATE TABLE IF NOT EXISTS plans (
    plan_id             INT AUTO_INCREMENT PRIMARY KEY,
    company_id          INT NOT NULL,
    plan_name           VARCHAR(100) NOT NULL,
    plan_description    TEXT,
    monthly_fee         DECIMAL(10,2) NOT NULL,
    speed_rate          VARCHAR(50),
    billing_cycle       VARCHAR(20) DEFAULT 'Monthly',
    status              VARCHAR(20) DEFAULT 'Active',
    created_at          DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(company_id)
)";
executeSql($db, $sql, "Created plans table");

// Create subscriber_plans table
$sql = "CREATE TABLE IF NOT EXISTS subscriber_plans (
    subscriber_plan_id  INT AUTO_INCREMENT PRIMARY KEY,
    subscriber_id       INT NOT NULL,
    plan_id             INT NOT NULL,
    start_date          DATE NOT NULL,
    end_date            DATE,
    status              VARCHAR(20) DEFAULT 'Active',
    notes               TEXT,
    created_at          DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (subscriber_id) REFERENCES subscribers(subscriber_id),
    FOREIGN KEY (plan_id) REFERENCES plans(plan_id)
)";
executeSql($db, $sql, "Created subscriber_plans table");

// Create statements table
$sql = "CREATE TABLE IF NOT EXISTS statements (
    statement_id        INT AUTO_INCREMENT PRIMARY KEY,
    company_id          INT NOT NULL,
    subscriber_id       INT NOT NULL,
    statement_no        VARCHAR(50) NOT NULL,
    bill_period_start   DATE NOT NULL,
    bill_period_end     DATE NOT NULL,
    amount              DECIMAL(10,2) NOT NULL,
    tax_amount          DECIMAL(10,2) DEFAULT 0.00,
    discount_amount     DECIMAL(10,2) DEFAULT 0.00,
    total_amount        DECIMAL(10,2) NOT NULL,
    unpaid_amount       DECIMAL(10,2) NOT NULL,
    due_date            DATE NOT NULL,
    status              VARCHAR(20) DEFAULT 'Unpaid',
    notes               TEXT,
    created_at          DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(company_id),
    FOREIGN KEY (subscriber_id) REFERENCES subscribers(subscriber_id),
    UNIQUE (company_id, statement_no)
)";
executeSql($db, $sql, "Created statements table");

// Create statement_items table
$sql = "CREATE TABLE IF NOT EXISTS statement_items (
    item_id             INT AUTO_INCREMENT PRIMARY KEY,
    statement_id        INT NOT NULL,
    description         VARCHAR(255) NOT NULL,
    amount              DECIMAL(10,2) NOT NULL,
    tax_rate            DECIMAL(5,2) DEFAULT 0.00,
    tax_amount          DECIMAL(10,2) DEFAULT 0.00,
    discount_amount     DECIMAL(10,2) DEFAULT 0.00,
    total_amount        DECIMAL(10,2) NOT NULL,
    created_at          DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (statement_id) REFERENCES statements(statement_id)
)";
executeSql($db, $sql, "Created statement_items table");

// Create payments table
$sql = "CREATE TABLE IF NOT EXISTS payments (
    payment_id          INT AUTO_INCREMENT PRIMARY KEY,
    company_id          INT NOT NULL,
    statement_id        INT NOT NULL,
    or_no               VARCHAR(50) NOT NULL,
    or_date             DATE NOT NULL,
    paid_amount         DECIMAL(10,2) NOT NULL,
    adv_payment         DECIMAL(10,2) DEFAULT 0.00,
    payment_method      VARCHAR(50) NOT NULL,
    payment_date        DATE NOT NULL,
    payment_status      VARCHAR(20) DEFAULT 'Completed',
    notes               TEXT,
    created_by          INT,
    created_at          DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(company_id),
    FOREIGN KEY (statement_id) REFERENCES statements(statement_id),
    FOREIGN KEY (created_by) REFERENCES users(user_id),
    UNIQUE (company_id, or_no)
)";
executeSql($db, $sql, "Created payments table");

// Create company_settings table
$sql = "CREATE TABLE IF NOT EXISTS company_settings (
    setting_id          INT AUTO_INCREMENT PRIMARY KEY,
    company_id          INT NOT NULL,
    setting_key         VARCHAR(100) NOT NULL,
    setting_value       TEXT,
    created_at          DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(company_id),
    UNIQUE (company_id, setting_key)
)";
executeSql($db, $sql, "Created company_settings table");

// Create audit_logs table
$sql = "CREATE TABLE IF NOT EXISTS audit_logs (
    log_id              INT AUTO_INCREMENT PRIMARY KEY,
    company_id          INT NOT NULL,
    user_id             INT NOT NULL,
    action              VARCHAR(100) NOT NULL,
    table_name          VARCHAR(100),
    record_id           INT,
    old_values          JSON,
    new_values          JSON,
    ip_address          VARCHAR(45),
    created_at          DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(company_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
)";
executeSql($db, $sql, "Created audit_logs table");

echo "\nDatabase schema creation completed!\n";
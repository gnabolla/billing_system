<?php

/**
 * Class SubscriberAuth
 *
 * Handles authentication for subscribers
 */
class SubscriberAuth
{
    protected $db;

    /**
     * SubscriberAuth constructor.
     *
     * @param Database $database Database connection
     */
    public function __construct($database)
    {
        $this->db = $database;
        
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Attempt to log in a subscriber
     * 
     * @param string $accountNo Account number
     * @param string $password Password
     * @return bool Success or failure
     */
    public function login($accountNo, $password)
    {
        // Get subscriber by account number
        $sql = "SELECT s.*, sa.password_hash 
                FROM subscribers s
                JOIN subscriber_auth sa ON s.subscriber_id = sa.subscriber_id
                WHERE s.account_no = :account_no
                LIMIT 1";
                
        $stmt = $this->db->query($sql, ['account_no' => $accountNo]);
        $subscriber = $stmt->fetch();
        
        if (!$subscriber || !$this->verifyPassword($password, $subscriber['password_hash'])) {
            return false;
        }

        // Check if subscriber is active
        if ($subscriber['status'] !== 'Active') {
            return false;
        }

        // Update last login time
        $this->updateLastLogin($subscriber['subscriber_id']);

        // Set session data
        $_SESSION['subscriber_id'] = $subscriber['subscriber_id'];
        $_SESSION['account_no'] = $subscriber['account_no'];
        $_SESSION['company_id'] = $subscriber['company_id'];
        $_SESSION['subscriber_name'] = !empty($subscriber['company_name']) ? 
            $subscriber['company_name'] : 
            $subscriber['first_name'] . ' ' . $subscriber['last_name'];
        $_SESSION['subscriber_logged_in'] = true;

        return true;
    }

    /**
     * Verify the subscriber's password
     * 
     * @param string $password Plain text password
     * @param string $hash The hashed password to compare against
     * @return bool True if password matches
     */
    public function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * Log out the current subscriber
     */
    public function logout()
    {
        // Unset all subscriber-related session variables
        unset($_SESSION['subscriber_id']);
        unset($_SESSION['account_no']);
        unset($_SESSION['company_id']);
        unset($_SESSION['subscriber_name']);
        unset($_SESSION['subscriber_logged_in']);
        
        // Could also destroy the entire session if needed
        // session_destroy();
    }

    /**
     * Check if subscriber is logged in
     * 
     * @return bool
     */
    public function isLoggedIn()
    {
        return isset($_SESSION['subscriber_logged_in']) && $_SESSION['subscriber_logged_in'] === true;
    }

    /**
     * Get current subscriber data
     * 
     * @return array|bool Subscriber data or false if not logged in
     */
    public function getCurrentSubscriber()
    {
        if (!$this->isLoggedIn()) {
            return false;
        }

        $sql = "SELECT * FROM subscribers WHERE subscriber_id = :subscriber_id LIMIT 1";
        $stmt = $this->db->query($sql, ['subscriber_id' => $_SESSION['subscriber_id']]);
        
        return $stmt->fetch() ?: false;
    }

    /**
     * Update subscriber's last login time
     * 
     * @param int $subscriberId Subscriber ID
     * @return bool Success or failure
     */
    public function updateLastLogin($subscriberId)
    {
        $sql = "UPDATE subscriber_auth 
                SET last_login = :last_login 
                WHERE subscriber_id = :subscriber_id";
                
        $params = [
            'last_login' => date('Y-m-d H:i:s'),
            'subscriber_id' => $subscriberId
        ];

        try {
            $stmt = $this->db->query($sql, $params);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error updating last login: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Register a new subscriber auth account
     * 
     * @param int $subscriberId The subscriber ID
     * @param string $password The password (will be hashed)
     * @return bool Success or failure
     */
    public function register($subscriberId, $password)
    {
        // Check if auth record already exists
        $sql = "SELECT subscriber_auth_id FROM subscriber_auth WHERE subscriber_id = :subscriber_id LIMIT 1";
        $stmt = $this->db->query($sql, ['subscriber_id' => $subscriberId]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            return false; // Auth record already exists
        }
        
        // Create password hash
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert new auth record
        $sql = "INSERT INTO subscriber_auth (subscriber_id, password_hash, created_at, updated_at) 
                VALUES (:subscriber_id, :password_hash, :created_at, :updated_at)";
                
        $params = [
            'subscriber_id' => $subscriberId,
            'password_hash' => $passwordHash,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        try {
            $stmt = $this->db->query($sql, $params);
            return $this->db->connection->lastInsertId() > 0;
        } catch (PDOException $e) {
            error_log("Error creating subscriber auth: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update subscriber password
     * 
     * @param int $subscriberId Subscriber ID
     * @param string $newPassword New password (will be hashed)
     * @return bool Success or failure
     */
    public function updatePassword($subscriberId, $newPassword)
    {
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);

        $sql = "UPDATE subscriber_auth 
                SET password_hash = :password_hash, 
                    updated_at = :updated_at,
                    password_reset_token = NULL,
                    password_reset_expires = NULL
                WHERE subscriber_id = :subscriber_id";

        $params = [
            'password_hash' => $passwordHash,
            'updated_at' => date('Y-m-d H:i:s'),
            'subscriber_id' => $subscriberId
        ];

        try {
            $stmt = $this->db->query($sql, $params);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error updating password: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Create a password reset token
     * 
     * @param string $accountNo Account number or email
     * @return string|bool The token or false on failure
     */
    public function createPasswordResetToken($accountNoOrEmail)
    {
        // Find subscriber by account number or email
        $sql = "SELECT s.subscriber_id, s.account_no, s.email 
                FROM subscribers s
                WHERE s.account_no = :account_no OR s.email = :email
                LIMIT 1";
                
        $stmt = $this->db->query($sql, [
            'account_no' => $accountNoOrEmail,
            'email' => $accountNoOrEmail
        ]);
        
        $subscriber = $stmt->fetch();
        
        if (!$subscriber) {
            return false;
        }
        
        // Generate a unique token
        $token = bin2hex(random_bytes(32));
        
        // Set expiration time (24 hours from now)
        $expires = date('Y-m-d H:i:s', time() + 86400);
        
        // Save token in database
        $sql = "UPDATE subscriber_auth 
                SET password_reset_token = :token, 
                    password_reset_expires = :expires,
                    updated_at = :updated_at
                WHERE subscriber_id = :subscriber_id";
                
        $params = [
            'token' => $token,
            'expires' => $expires,
            'updated_at' => date('Y-m-d H:i:s'),
            'subscriber_id' => $subscriber['subscriber_id']
        ];
        
        try {
            $stmt = $this->db->query($sql, $params);
            if ($stmt->rowCount() === 0) {
                // Create auth record if it doesn't exist
                $this->register($subscriber['subscriber_id'], bin2hex(random_bytes(8)));
                
                // Try again with the token
                $stmt = $this->db->query($sql, $params);
            }
            
            if ($stmt->rowCount() > 0) {
                return [
                    'token' => $token,
                    'subscriber_id' => $subscriber['subscriber_id'],
                    'account_no' => $subscriber['account_no'],
                    'email' => $subscriber['email']
                ];
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Error creating password reset token: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify a password reset token
     * 
     * @param int $subscriberId Subscriber ID
     * @param string $token The token to verify
     * @return bool True if token is valid
     */
    public function verifyPasswordResetToken($subscriberId, $token)
    {
        $sql = "SELECT password_reset_expires 
                FROM subscriber_auth 
                WHERE subscriber_id = :subscriber_id 
                AND password_reset_token = :token
                LIMIT 1";
                
        $stmt = $this->db->query($sql, [
            'subscriber_id' => $subscriberId,
            'token' => $token
        ]);
        
        $result = $stmt->fetch();
        
        if (!$result) {
            return false;
        }
        
        // Check if token has expired
        return strtotime($result['password_reset_expires']) > time();
    }

    /**
     * Reset password using token
     * 
     * @param int $subscriberId Subscriber ID
     * @param string $token Reset token
     * @param string $newPassword New password
     * @return bool Success or failure
     */
    public function resetPassword($subscriberId, $token, $newPassword)
    {
        if (!$this->verifyPasswordResetToken($subscriberId, $token)) {
            return false;
        }
        
        return $this->updatePassword($subscriberId, $newPassword);
    }
}
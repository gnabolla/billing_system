<?php

class Auth
{
    protected $db;
    protected $user;
    protected $company;

    public function __construct($database)
    {
        $this->db = $database;
        
        // Load models
        require_once __DIR__ . '/../models/User.php';
        require_once __DIR__ . '/../models/Company.php';
        
        $this->user = new User($database);
        $this->company = new Company($database);
        
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Register a new company and admin user
     * 
     * @param array $companyData Company data
     * @param array $userData Admin user data
     * @return array|bool Success data or false on failure
     */
    public function registerCompany($companyData, $userData)
    {
        // Begin transaction
        $this->db->connection->beginTransaction();

        try {
            // Create company
            $companyId = $this->company->create($companyData);
            if (!$companyId) {
                throw new Exception("Failed to create company");
            }

            // Add company ID to user data and set role as admin
            $userData['company_id'] = $companyId;
            $userData['role'] = 'admin';

            // Create user
            $userId = $this->user->create($userData);
            if (!$userId) {
                throw new Exception("Failed to create user");
            }

            // Commit transaction
            $this->db->connection->commit();

            return [
                'company_id' => $companyId,
                'user_id' => $userId
            ];
        } catch (Exception $e) {
            // Rollback transaction on failure
            $this->db->connection->rollBack();
            error_log("Registration failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Attempt to log in a user
     * 
     * @param string $username Username or email
     * @param string $password Password
     * @return bool Success or failure
     */
    public function login($username, $password)
    {
        // Determine if input is email or username
        $user = filter_var($username, FILTER_VALIDATE_EMAIL) 
            ? $this->user->getByEmail($username) 
            : $this->user->getByUsername($username);

        if (!$user || !$this->user->verifyPassword($password, $user['password_hash'])) {
            return false;
        }

        // Check if user is active
        if ($user['status'] !== 'Active') {
            return false;
        }

        // Get company info
        $company = $this->company->getById($user['company_id']);
        if (!$company || $company['subscription_status'] !== 'Active') {
            return false;
        }

        // Update last login time
        $this->user->updateLastLogin($user['user_id']);

        // Set session data
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['company_id'] = $user['company_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['company_name'] = $company['company_name'];
        $_SESSION['logged_in'] = true;

        return true;
    }

    /**
     * Log out the current user
     */
    public function logout()
    {
        // Unset all session variables
        $_SESSION = [];

        // Delete the session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // Destroy the session
        session_destroy();
    }

    /**
     * Check if user is logged in
     * 
     * @return bool
     */
    public function isLoggedIn()
    {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    /**
     * Check if current user has specific role
     * 
     * @param string|array $roles Role or roles to check
     * @return bool
     */
    public function hasRole($roles)
    {
        if (!$this->isLoggedIn()) {
            return false;
        }

        if (is_string($roles)) {
            return $_SESSION['role'] === $roles;
        }

        if (is_array($roles)) {
            return in_array($_SESSION['role'], $roles);
        }

        return false;
    }

    /**
     * Get current user data
     * 
     * @return array|bool User data or false if not logged in
     */
    public function getCurrentUser()
    {
        if (!$this->isLoggedIn()) {
            return false;
        }

        return $this->user->getById($_SESSION['user_id']);
    }

    /**
     * Get current company data
     * 
     * @return array|bool Company data or false if not logged in
     */
    public function getCurrentCompany()
    {
        if (!$this->isLoggedIn()) {
            return false;
        }

        return $this->company->getById($_SESSION['company_id']);
    }
}
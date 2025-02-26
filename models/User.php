<?php

class User
{
    protected $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    /**
     * Create a new user
     * 
     * @param array $userData User data to insert
     * @return int|bool The ID of the new user or false on failure
     */
    public function create($userData)
    {
        // Hash the password
        $userData['password_hash'] = password_hash($userData['password'], PASSWORD_DEFAULT);
        unset($userData['password']);

        // Set created_at and updated_at
        $userData['created_at'] = date('Y-m-d H:i:s');
        $userData['updated_at'] = date('Y-m-d H:i:s');

        // Build the SQL query
        $columns = implode(', ', array_keys($userData));
        $placeholders = ':' . implode(', :', array_keys($userData));

        $sql = "INSERT INTO users ($columns) VALUES ($placeholders)";

        try {
            $stmt = $this->db->query($sql, $userData);
            return $this->db->connection->lastInsertId();
        } catch (PDOException $e) {
            // Log error
            error_log("Error creating user: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user by email
     * 
     * @param string $email Email to search for
     * @return array|bool User data or false if not found
     */
    public function getByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->db->query($sql, ['email' => $email]);
        
        return $stmt->fetch() ?: false;
    }

    /**
     * Get user by username
     * 
     * @param string $username Username to search for
     * @return array|bool User data or false if not found
     */
    public function getByUsername($username)
    {
        $sql = "SELECT * FROM users WHERE username = :username LIMIT 1";
        $stmt = $this->db->query($sql, ['username' => $username]);
        
        return $stmt->fetch() ?: false;
    }

    /**
     * Get user by ID
     * 
     * @param int $id User ID
     * @return array|bool User data or false if not found
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM users WHERE user_id = :id LIMIT 1";
        $stmt = $this->db->query($sql, ['id' => $id]);
        
        return $stmt->fetch() ?: false;
    }

    /**
     * Update a user's last login time
     * 
     * @param int $userId User ID
     * @return bool Success or failure
     */
    public function updateLastLogin($userId)
    {
        $sql = "UPDATE users SET last_login = :last_login WHERE user_id = :user_id";
        $params = [
            'last_login' => date('Y-m-d H:i:s'),
            'user_id' => $userId
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
     * Verify the user's password
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
     * Update user profile data
     * 
     * @param int $userId User ID
     * @param array $userData User data to update
     * @return bool Success or failure
     */
    public function update($userId, $userData)
    {
        // Add updated_at timestamp
        $userData['updated_at'] = date('Y-m-d H:i:s');

        // Build SET clause for SQL
        $setClause = '';
        foreach ($userData as $column => $value) {
            $setClause .= "$column = :$column, ";
        }
        $setClause = rtrim($setClause, ', ');

        $sql = "UPDATE users SET $setClause WHERE user_id = :user_id";
        $userData['user_id'] = $userId;

        try {
            $stmt = $this->db->query($sql, $userData);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error updating user: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update user password
     * 
     * @param int $userId User ID
     * @param string $newPassword New password (will be hashed)
     * @return bool Success or failure
     */
    public function updatePassword($userId, $newPassword)
    {
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);

        $sql = "UPDATE users SET 
                password_hash = :password_hash, 
                updated_at = :updated_at 
                WHERE user_id = :user_id";

        $params = [
            'password_hash' => $passwordHash,
            'updated_at' => date('Y-m-d H:i:s'),
            'user_id' => $userId
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
     * Get all users for a company
     * 
     * @param int $companyId Company ID
     * @return array Array of users
     */
    public function getAllByCompany($companyId)
    {
        $sql = "SELECT * FROM users WHERE company_id = :company_id ORDER BY username";
        $stmt = $this->db->query($sql, ['company_id' => $companyId]);
        
        return $stmt->fetchAll();
    }
}
<?php

class Company
{
    protected $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    /**
     * Create a new company
     * 
     * @param array $companyData Company data to insert
     * @return int|bool The ID of the new company or false on failure
     */
    public function create($companyData)
    {
        // Set created_at and updated_at
        $companyData['created_at'] = date('Y-m-d H:i:s');
        $companyData['updated_at'] = date('Y-m-d H:i:s');

        // Set default subscription status if not provided
        if (!isset($companyData['subscription_status'])) {
            $companyData['subscription_status'] = 'Active';
        }

        // Set default subscription plan if not provided
        if (!isset($companyData['subscription_plan'])) {
            $companyData['subscription_plan'] = 'Basic';
        }

        // Build the SQL query
        $columns = implode(', ', array_keys($companyData));
        $placeholders = ':' . implode(', :', array_keys($companyData));

        $sql = "INSERT INTO companies ($columns) VALUES ($placeholders)";

        try {
            $stmt = $this->db->query($sql, $companyData);
            return $this->db->connection->lastInsertId();
        } catch (PDOException $e) {
            // Log error
            error_log("Error creating company: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get company by ID
     * 
     * @param int $id Company ID
     * @return array|bool Company data or false if not found
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM companies WHERE company_id = :id LIMIT 1";
        $stmt = $this->db->query($sql, ['id' => $id]);
        
        return $stmt->fetch() ?: false;
    }

    /**
     * Get company by name
     * 
     * @param string $name Company name to search for
     * @return array|bool Company data or false if not found
     */
    public function getByName($name)
    {
        $sql = "SELECT * FROM companies WHERE company_name = :name LIMIT 1";
        $stmt = $this->db->query($sql, ['name' => $name]);
        
        return $stmt->fetch() ?: false;
    }

    /**
     * Get all companies (with optional pagination)
     * 
     * @param int $limit Number of results per page
     * @param int $offset Offset for pagination
     * @return array Array of companies
     */
    public function getAll($limit = 10, $offset = 0)
    {
        $sql = "SELECT * FROM companies ORDER BY company_name LIMIT :limit OFFSET :offset";
        $stmt = $this->db->query($sql, [
            'limit' => $limit,
            'offset' => $offset
        ]);
        
        return $stmt->fetchAll();
    }

    /**
     * Update company
     * 
     * @param int $id Company ID
     * @param array $data Company data to update
     * @return bool Success or failure
     */
    public function update($id, $data)
    {
        // Add updated_at timestamp
        $data['updated_at'] = date('Y-m-d H:i:s');

        // Build SET clause for SQL
        $setClause = '';
        foreach ($data as $column => $value) {
            $setClause .= "$column = :$column, ";
        }
        $setClause = rtrim($setClause, ', ');

        $sql = "UPDATE companies SET $setClause WHERE company_id = :company_id";
        $data['company_id'] = $id;

        try {
            $stmt = $this->db->query($sql, $data);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error updating company: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Save company settings
     * 
     * @param int $companyId Company ID
     * @param string $key Setting key
     * @param mixed $value Setting value
     * @return bool Success or failure
     */
    public function saveSetting($companyId, $key, $value)
    {
        // Check if setting already exists
        $sql = "SELECT setting_id FROM company_settings 
                WHERE company_id = :company_id AND setting_key = :key LIMIT 1";
        $stmt = $this->db->query($sql, [
            'company_id' => $companyId,
            'key' => $key
        ]);
        $existing = $stmt->fetch();

        if ($existing) {
            // Update existing setting
            $sql = "UPDATE company_settings 
                    SET setting_value = :value, updated_at = :updated_at 
                    WHERE setting_id = :setting_id";
            $params = [
                'value' => $value,
                'updated_at' => date('Y-m-d H:i:s'),
                'setting_id' => $existing['setting_id']
            ];
        } else {
            // Insert new setting
            $sql = "INSERT INTO company_settings 
                    (company_id, setting_key, setting_value, created_at, updated_at) 
                    VALUES (:company_id, :key, :value, :created_at, :updated_at)";
            $params = [
                'company_id' => $companyId,
                'key' => $key,
                'value' => $value,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }

        try {
            $stmt = $this->db->query($sql, $params);
            return true;
        } catch (PDOException $e) {
            error_log("Error saving company setting: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get company setting
     * 
     * @param int $companyId Company ID
     * @param string $key Setting key
     * @param mixed $default Default value if setting not found
     * @return mixed Setting value or default
     */
    public function getSetting($companyId, $key, $default = null)
    {
        $sql = "SELECT setting_value FROM company_settings 
                WHERE company_id = :company_id AND setting_key = :key LIMIT 1";
        $stmt = $this->db->query($sql, [
            'company_id' => $companyId,
            'key' => $key
        ]);
        
        $result = $stmt->fetch();
        return ($result) ? $result['setting_value'] : $default;
    }
}
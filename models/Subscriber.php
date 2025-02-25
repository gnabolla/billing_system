<?php

class Subscriber
{
    protected $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    /**
     * Get all subscribers for a company
     * 
     * @param int $companyId The company ID
     * @param array $filters Optional filters (status, search, etc.)
     * @param int $limit Number of results per page
     * @param int $offset Offset for pagination
     * @return array Array of subscribers
     */
    public function getAllByCompany($companyId, $filters = [], $limit = 50, $offset = 0)
    {
        $params = ['company_id' => $companyId];
        $whereConditions = ['company_id = :company_id'];
        
        // Apply filters if provided
        if (!empty($filters['status'])) {
            $whereConditions[] = 'status = :status';
            $params['status'] = $filters['status'];
        }
        
        // Search by name, account number, or phone
        if (!empty($filters['search'])) {
            $whereConditions[] = '(account_no LIKE :search OR 
                                   company_name LIKE :search OR 
                                   CONCAT(first_name, " ", last_name) LIKE :search OR
                                   phone_number LIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
        }
        
        $whereClause = implode(' AND ', $whereConditions);
        
        $sql = "SELECT * FROM subscribers 
                WHERE $whereClause 
                ORDER BY created_at DESC 
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->query($sql, array_merge($params, [
            'limit' => $limit,
            'offset' => $offset
        ]));
        
        return $stmt->fetchAll();
    }
    
    /**
     * Count total subscribers for a company
     * 
     * @param int $companyId The company ID
     * @param array $filters Optional filters
     * @return int Total count
     */
    public function countByCompany($companyId, $filters = [])
    {
        $params = ['company_id' => $companyId];
        $whereConditions = ['company_id = :company_id'];
        
        // Apply filters if provided
        if (!empty($filters['status'])) {
            $whereConditions[] = 'status = :status';
            $params['status'] = $filters['status'];
        }
        
        // Search by name, account number, or phone
        if (!empty($filters['search'])) {
            $whereConditions[] = '(account_no LIKE :search OR 
                                   company_name LIKE :search OR 
                                   CONCAT(first_name, " ", last_name) LIKE :search OR
                                   phone_number LIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
        }
        
        $whereClause = implode(' AND ', $whereConditions);
        
        $sql = "SELECT COUNT(*) as count FROM subscribers WHERE $whereClause";
        $stmt = $this->db->query($sql, $params);
        $result = $stmt->fetch();
        
        return (int) $result['count'];
    }
    
    /**
     * Get subscriber by ID
     * 
     * @param int $id Subscriber ID
     * @param int $companyId Company ID (for security)
     * @return array|bool Subscriber data or false if not found
     */
    public function getById($id, $companyId)
    {
        $sql = "SELECT * FROM subscribers 
                WHERE subscriber_id = :id AND company_id = :company_id 
                LIMIT 1";
                
        $stmt = $this->db->query($sql, [
            'id' => $id,
            'company_id' => $companyId
        ]);
        
        return $stmt->fetch() ?: false;
    }
    
    /**
     * Get subscriber by account number
     * 
     * @param string $accountNo Account number
     * @param int $companyId Company ID (for security)
     * @return array|bool Subscriber data or false if not found
     */
    public function getByAccountNo($accountNo, $companyId)
    {
        $sql = "SELECT * FROM subscribers 
                WHERE account_no = :account_no AND company_id = :company_id 
                LIMIT 1";
                
        $stmt = $this->db->query($sql, [
            'account_no' => $accountNo,
            'company_id' => $companyId
        ]);
        
        return $stmt->fetch() ?: false;
    }
    
    /**
     * Create a new subscriber
     * 
     * @param array $data Subscriber data
     * @return int|bool The ID of the new subscriber or false on failure
     */
    public function create($data)
    {
        // Set timestamps
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        // If registration_date is not provided, use current date
        if (!isset($data['registration_date']) || empty($data['registration_date'])) {
            $data['registration_date'] = date('Y-m-d');
        }
        
        // If status is not provided, set as Active
        if (!isset($data['status'])) {
            $data['status'] = 'Active';
        }
        
        // Build the SQL query
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO subscribers ($columns) VALUES ($placeholders)";
        
        try {
            $stmt = $this->db->query($sql, $data);
            return $this->db->connection->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error creating subscriber: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update a subscriber
     * 
     * @param int $id Subscriber ID
     * @param array $data Data to update
     * @param int $companyId Company ID (for security)
     * @return bool Success or failure
     */
    public function update($id, $data, $companyId)
    {
        // Add updated_at timestamp
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        // Build SET clause for SQL
        $setClause = '';
        foreach ($data as $column => $value) {
            $setClause .= "$column = :$column, ";
        }
        $setClause = rtrim($setClause, ', ');
        
        $sql = "UPDATE subscribers 
                SET $setClause 
                WHERE subscriber_id = :subscriber_id AND company_id = :company_id";
                
        $data['subscriber_id'] = $id;
        $data['company_id'] = $companyId;
        
        try {
            $stmt = $this->db->query($sql, $data);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error updating subscriber: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete a subscriber
     * 
     * @param int $id Subscriber ID
     * @param int $companyId Company ID (for security)
     * @return bool Success or failure
     */
    public function delete($id, $companyId)
    {
        // Check if subscriber has any related records before deleting
        
        // For now, soft delete by updating status
        $sql = "UPDATE subscribers 
                SET status = 'Deleted', updated_at = :updated_at 
                WHERE subscriber_id = :id AND company_id = :company_id";
                
        try {
            $stmt = $this->db->query($sql, [
                'updated_at' => date('Y-m-d H:i:s'),
                'id' => $id,
                'company_id' => $companyId
            ]);
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error deleting subscriber: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Generate a unique account number
     * 
     * @param int $companyId Company ID
     * @return string Unique account number
     */
    public function generateAccountNumber($companyId)
    {
        // Get current year
        $year = date('Y');
        
        // Get count of subscribers for this company in current year
        $sql = "SELECT COUNT(*) as count FROM subscribers 
                WHERE company_id = :company_id 
                AND YEAR(created_at) = :year";
                
        $stmt = $this->db->query($sql, [
            'company_id' => $companyId,
            'year' => $year
        ]);
        
        $result = $stmt->fetch();
        $count = (int) $result['count'] + 1;
        
        // Format: YYYY-CompanyID-SequentialNumber (padded to 5 digits)
        return $year . '-' . $companyId . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }
}
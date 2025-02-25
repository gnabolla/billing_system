<?php

class Plan
{
    protected $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    /**
     * Get all plans for a company
     * 
     * @param int $companyId The company ID
     * @param array $filters Optional filters (status, search, etc.)
     * @param int $limit Number of results per page
     * @param int $offset Offset for pagination
     * @return array Array of plans
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

        // Search by name or description
        if (!empty($filters['search'])) {
            $whereConditions[] = '(plan_name LIKE :search OR plan_description LIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $whereClause = implode(' AND ', $whereConditions);

        $sql = "SELECT * FROM plans 
        WHERE $whereClause 
        ORDER BY plan_name ASC 
        LIMIT " . (int)$limit . " OFFSET " . (int)$offset;

        $stmt = $this->db->query($sql, $params);

        return $stmt->fetchAll();
    }

    /**
     * Count total plans for a company
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

        // Search by name or description
        if (!empty($filters['search'])) {
            $whereConditions[] = '(plan_name LIKE :search OR plan_description LIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $whereClause = implode(' AND ', $whereConditions);

        $sql = "SELECT COUNT(*) as count FROM plans WHERE $whereClause";
        $stmt = $this->db->query($sql, $params);
        $result = $stmt->fetch();

        return (int) $result['count'];
    }

    /**
     * Get plan by ID
     * 
     * @param int $id Plan ID
     * @param int $companyId Company ID (for security)
     * @return array|bool Plan data or false if not found
     */
    public function getById($id, $companyId)
    {
        $sql = "SELECT * FROM plans 
                WHERE plan_id = :id AND company_id = :company_id 
                LIMIT 1";

        $stmt = $this->db->query($sql, [
            'id' => $id,
            'company_id' => $companyId
        ]);

        return $stmt->fetch() ?: false;
    }

    /**
     * Create a new plan
     * 
     * @param array $data Plan data
     * @return int|bool The ID of the new plan or false on failure
     */
    public function create($data)
    {
        // Set timestamps
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        // If status is not provided, set as Active
        if (!isset($data['status'])) {
            $data['status'] = 'Active';
        }

        // Build the SQL query
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO plans ($columns) VALUES ($placeholders)";

        try {
            $stmt = $this->db->query($sql, $data);
            return $this->db->connection->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error creating plan: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update a plan
     * 
     * @param int $id Plan ID
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

        $sql = "UPDATE plans 
                SET $setClause 
                WHERE plan_id = :plan_id AND company_id = :company_id";

        $data['plan_id'] = $id;
        $data['company_id'] = $companyId;

        try {
            $stmt = $this->db->query($sql, $data);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error updating plan: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a plan
     * 
     * @param int $id Plan ID
     * @param int $companyId Company ID (for security)
     * @return bool Success or failure
     */
    public function delete($id, $companyId)
    {
        // First check if the plan is in use
        $sql = "SELECT COUNT(*) as count FROM subscriber_plans 
                WHERE plan_id = :plan_id";
        
        $stmt = $this->db->query($sql, ['plan_id' => $id]);
        $result = $stmt->fetch();
        
        if ((int) $result['count'] > 0) {
            // Plan is in use, perform soft delete by updating status
            $sql = "UPDATE plans 
                    SET status = 'Inactive', updated_at = :updated_at 
                    WHERE plan_id = :id AND company_id = :company_id";

            try {
                $stmt = $this->db->query($sql, [
                    'updated_at' => date('Y-m-d H:i:s'),
                    'id' => $id,
                    'company_id' => $companyId
                ]);

                return $stmt->rowCount() > 0;
            } catch (PDOException $e) {
                error_log("Error soft-deleting plan: " . $e->getMessage());
                return false;
            }
        } else {
            // Plan is not in use, can be hard deleted
            $sql = "DELETE FROM plans 
                    WHERE plan_id = :id AND company_id = :company_id";

            try {
                $stmt = $this->db->query($sql, [
                    'id' => $id,
                    'company_id' => $companyId
                ]);

                return $stmt->rowCount() > 0;
            } catch (PDOException $e) {
                error_log("Error deleting plan: " . $e->getMessage());
                return false;
            }
        }
    }

    /**
     * Get active plans for a subscriber
     * 
     * @param int $subscriberId The subscriber ID
     * @param int $companyId Company ID (for security)
     * @return array Array of active plans for the subscriber
     */
    public function getActiveForSubscriber($subscriberId, $companyId)
    {
        $sql = "SELECT p.*, sp.subscriber_plan_id, sp.start_date, sp.end_date, sp.status as subscription_status 
                FROM plans p
                JOIN subscriber_plans sp ON p.plan_id = sp.plan_id 
                WHERE sp.subscriber_id = :subscriber_id 
                AND p.company_id = :company_id 
                AND sp.status = 'Active'
                ORDER BY sp.start_date DESC";

        $stmt = $this->db->query($sql, [
            'subscriber_id' => $subscriberId,
            'company_id' => $companyId
        ]);

        return $stmt->fetchAll();
    }
}
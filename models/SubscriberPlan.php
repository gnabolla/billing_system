<?php

class SubscriberPlan
{
    protected $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    /**
     * Get all plans for a specific subscriber
     * 
     * @param int $subscriberId The subscriber ID
     * @param int $companyId Company ID (for security)
     * @return array Array of subscriber plans with plan details
     */
    public function getAllForSubscriber($subscriberId, $companyId)
    {
        // Simplified query - just join subscriber_plans and plans
        $sql = "SELECT sp.*, p.plan_name, p.monthly_fee, p.speed_rate, p.billing_cycle
                FROM subscriber_plans sp
                JOIN plans p ON sp.plan_id = p.plan_id
                WHERE sp.subscriber_id = :subscriber_id
                ORDER BY sp.start_date DESC";

        try {
            $stmt = $this->db->query($sql, [
                'subscriber_id' => $subscriberId
            ]);

            $plans = $stmt->fetchAll();
            error_log("Found " . count($plans) . " plans for subscriber ID: " . $subscriberId);
            return $plans;
        } catch (PDOException $e) {
            error_log("Error getting subscriber plans: " . $e->getMessage());
            error_log("SQL: " . $sql);
            error_log("Subscriber ID: " . $subscriberId);
            return [];
        }
    }

    /**
     * Get active plans for a specific subscriber
     * 
     * @param int $subscriberId The subscriber ID
     * @param int $companyId Company ID (for security)
     * @return array Array of active subscriber plans with plan details
     */
    public function getActiveForSubscriber($subscriberId, $companyId)
    {
        $sql = "SELECT sp.*, p.plan_name, p.monthly_fee, p.speed_rate, p.billing_cycle
                FROM subscriber_plans sp
                JOIN plans p ON sp.plan_id = p.plan_id
                JOIN subscribers s ON sp.subscriber_id = s.subscriber_id
                WHERE sp.subscriber_id = :subscriber_id
                AND s.company_id = :company_id
                AND sp.status = 'Active'
                ORDER BY sp.start_date DESC";

        $stmt = $this->db->query($sql, [
            'subscriber_id' => $subscriberId,
            'company_id' => $companyId
        ]);

        return $stmt->fetchAll();
    }

    /**
     * Get a specific subscriber plan by ID
     * 
     * @param int $id SubscriberPlan ID
     * @param int $companyId Company ID (for security)
     * @return array|bool Plan data or false if not found
     */
    public function getById($id, $companyId)
    {
        $sql = "SELECT sp.*, p.plan_name, p.monthly_fee, p.speed_rate, p.billing_cycle
                FROM subscriber_plans sp
                JOIN plans p ON sp.plan_id = p.plan_id
                JOIN subscribers s ON sp.subscriber_id = s.subscriber_id
                WHERE sp.subscriber_plan_id = :id
                AND s.company_id = :company_id
                LIMIT 1";

        $stmt = $this->db->query($sql, [
            'id' => $id,
            'company_id' => $companyId
        ]);

        return $stmt->fetch() ?: false;
    }

    /**
     * Create a new subscriber plan (assign plan to subscriber)
     * 
     * @param array $data Subscriber plan data
     * @return int|bool The ID of the new subscriber plan or false on failure
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

        // Ensure subscriber_id is set
        if (empty($data['subscriber_id'])) {
            error_log("Error creating subscriber plan: subscriber_id is missing");
            return false;
        }

        // Ensure plan_id is set
        if (empty($data['plan_id'])) {
            error_log("Error creating subscriber plan: plan_id is missing");
            return false;
        }

        // Build the SQL query
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO subscriber_plans ($columns) VALUES ($placeholders)";

        try {
            $stmt = $this->db->query($sql, $data);
            $newId = $this->db->connection->lastInsertId();
            error_log("Successfully created subscriber plan with ID: " . $newId);
            return $newId;
        } catch (PDOException $e) {
            error_log("Error creating subscriber plan: " . $e->getMessage());
            error_log("SQL: " . $sql);
            error_log("Data: " . print_r($data, true));
            return false;
        }
    }

    /**
     * Update a subscriber plan
     * 
     * @param int $id SubscriberPlan ID
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

        $sql = "UPDATE subscriber_plans sp
                JOIN subscribers s ON sp.subscriber_id = s.subscriber_id
                SET $setClause 
                WHERE sp.subscriber_plan_id = :subscriber_plan_id
                AND s.company_id = :company_id";

        $data['subscriber_plan_id'] = $id;
        $data['company_id'] = $companyId;

        try {
            $stmt = $this->db->query($sql, $data);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error updating subscriber plan: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Terminate a subscriber plan (set end date and status to Terminated)
     * 
     * @param int $id SubscriberPlan ID
     * @param string $endDate End date (YYYY-MM-DD)
     * @param int $companyId Company ID (for security)
     * @return bool Success or failure
     */
    public function terminate($id, $endDate, $companyId)
    {
        $sql = "UPDATE subscriber_plans sp
                JOIN subscribers s ON sp.subscriber_id = s.subscriber_id
                SET sp.status = 'Terminated', 
                    sp.end_date = :end_date, 
                    sp.updated_at = :updated_at
                WHERE sp.subscriber_plan_id = :id
                AND s.company_id = :company_id";

        try {
            $stmt = $this->db->query($sql, [
                'end_date' => $endDate,
                'updated_at' => date('Y-m-d H:i:s'),
                'id' => $id,
                'company_id' => $companyId
            ]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error terminating subscriber plan: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get count of active plans for a company
     * 
     * @param int $companyId Company ID
     * @return int Count of active plans
     */
    public function countActiveByCompany($companyId)
    {
        $sql = "SELECT COUNT(*) as count 
                FROM subscriber_plans sp
                JOIN subscribers s ON sp.subscriber_id = s.subscriber_id
                WHERE s.company_id = :company_id
                AND sp.status = 'Active'";

        $stmt = $this->db->query($sql, ['company_id' => $companyId]);
        $result = $stmt->fetch();

        return (int) $result['count'];
    }
}

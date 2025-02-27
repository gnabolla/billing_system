<?php

class Payment
{
    protected $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    /**
     * Get all payments for a company
     * 
     * @param int $companyId The company ID
     * @param array $filters Optional filters (status, search, etc.)
     * @param int $limit Number of results per page
     * @param int $offset Offset for pagination
     * @return array Array of payments
     */
    public function getAllByCompany($companyId, $filters = [], $limit = 50, $offset = 0)
    {
        $params = ['company_id' => $companyId];
        $whereConditions = ['p.company_id = :company_id'];

        // Apply filters if provided
        if (!empty($filters['status'])) {
            $whereConditions[] = 'p.payment_status = :status';
            $params['status'] = $filters['status'];
        }

        // Filter by subscriber if provided
        if (!empty($filters['subscriber_id'])) {
            $whereConditions[] = 's.subscriber_id = :subscriber_id';
            $params['subscriber_id'] = $filters['subscriber_id'];
        }

        // Filter by statement if provided
        if (!empty($filters['statement_id'])) {
            $whereConditions[] = 'p.statement_id = :statement_id';
            $params['statement_id'] = $filters['statement_id'];
        }

        // Search by payment reference or subscriber info
        if (!empty($filters['search'])) {
            $whereConditions[] = '(p.or_no LIKE :search OR 
                                  sub.account_no LIKE :search OR 
                                  sub.company_name LIKE :search OR 
                                  CONCAT(sub.first_name, " ", sub.last_name) LIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
        }

        // Filter by date range
        if (!empty($filters['date_from'])) {
            $whereConditions[] = 'p.payment_date >= :date_from';
            $params['date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $whereConditions[] = 'p.payment_date <= :date_to';
            $params['date_to'] = $filters['date_to'];
        }

        $whereClause = implode(' AND ', $whereConditions);

        $sql = "SELECT p.*, 
                s.statement_no, s.total_amount as statement_amount, 
                sub.account_no, sub.company_name, sub.first_name, sub.last_name
                FROM payments p
                JOIN statements s ON p.statement_id = s.statement_id
                JOIN subscribers sub ON s.subscriber_id = sub.subscriber_id
                WHERE $whereClause 
                ORDER BY p.payment_date DESC, p.created_at DESC 
                LIMIT " . (int)$limit . " OFFSET " . (int)$offset;

        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Count total payments for a company
     * 
     * @param int $companyId The company ID
     * @param array $filters Optional filters
     * @return int Total count
     */
    public function countByCompany($companyId, $filters = [])
    {
        $params = ['company_id' => $companyId];
        $whereConditions = ['p.company_id = :company_id'];

        // Apply filters if provided
        if (!empty($filters['status'])) {
            $whereConditions[] = 'p.payment_status = :status';
            $params['status'] = $filters['status'];
        }

        // Filter by subscriber if provided
        if (!empty($filters['subscriber_id'])) {
            $whereConditions[] = 's.subscriber_id = :subscriber_id';
            $params['subscriber_id'] = $filters['subscriber_id'];
        }

        // Filter by statement if provided
        if (!empty($filters['statement_id'])) {
            $whereConditions[] = 'p.statement_id = :statement_id';
            $params['statement_id'] = $filters['statement_id'];
        }

        // Search by payment reference or subscriber info
        if (!empty($filters['search'])) {
            $whereConditions[] = '(p.or_no LIKE :search OR 
                                  sub.account_no LIKE :search OR 
                                  sub.company_name LIKE :search OR 
                                  CONCAT(sub.first_name, " ", sub.last_name) LIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
        }

        // Filter by date range
        if (!empty($filters['date_from'])) {
            $whereConditions[] = 'p.payment_date >= :date_from';
            $params['date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $whereConditions[] = 'p.payment_date <= :date_to';
            $params['date_to'] = $filters['date_to'];
        }

        $whereClause = implode(' AND ', $whereConditions);

        $sql = "SELECT COUNT(*) as count 
                FROM payments p
                JOIN statements s ON p.statement_id = s.statement_id
                JOIN subscribers sub ON s.subscriber_id = sub.subscriber_id
                WHERE $whereClause";

        $stmt = $this->db->query($sql, $params);
        $result = $stmt->fetch();

        return (int) $result['count'];
    }

    /**
     * Get payment by ID
     * 
     * @param int $id Payment ID
     * @param int $companyId Company ID (for security)
     * @return array|bool Payment data or false if not found
     */
    public function getById($id, $companyId)
    {
        $sql = "SELECT p.*, 
                s.statement_no, s.total_amount as statement_amount, s.subscriber_id,
                sub.account_no, sub.company_name, sub.first_name, sub.last_name, 
                sub.address, sub.phone_number, sub.email
                FROM payments p
                JOIN statements s ON p.statement_id = s.statement_id
                JOIN subscribers sub ON s.subscriber_id = sub.subscriber_id
                WHERE p.payment_id = :id AND p.company_id = :company_id 
                LIMIT 1";

        $stmt = $this->db->query($sql, [
            'id' => $id,
            'company_id' => $companyId
        ]);

        return $stmt->fetch() ?: false;
    }

    /**
     * Generate a unique OR number
     * 
     * @param int $companyId Company ID
     * @return string Unique OR number
     */
    public function generateORNumber($companyId)
    {
        // Get current year and month
        $year = date('Y');
        $month = date('m');

        // Get count of payments for this company in current month/year
        $sql = "SELECT COUNT(*) as count FROM payments 
                WHERE company_id = :company_id 
                AND YEAR(created_at) = :year 
                AND MONTH(created_at) = :month";

        $stmt = $this->db->query($sql, [
            'company_id' => $companyId,
            'year' => $year,
            'month' => $month
        ]);

        $result = $stmt->fetch();
        $count = (int) $result['count'] + 1;

        // Format: OR-YYYYMM-CompanyID-SequentialNumber (padded to 4 digits)
        return 'OR-' . $year . $month . '-' . $companyId . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Create a new payment
     * 
     * @param array $data Payment data
     * @return int|bool The ID of the new payment or false on failure
     */
    public function create($data)
    {
        // Begin transaction
        $this->db->connection->beginTransaction();

        try {
            // Set timestamps
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');

            // If or_no is not provided, generate one
            if (empty($data['or_no'])) {
                $data['or_no'] = $this->generateORNumber($data['company_id']);
            }

            // Set defaults for empty fields
            if (empty($data['or_date'])) {
                $data['or_date'] = date('Y-m-d');
            }

            if (empty($data['payment_date'])) {
                $data['payment_date'] = date('Y-m-d');
            }

            if (empty($data['payment_status'])) {
                $data['payment_status'] = 'Completed';
            }

            if (!isset($data['adv_payment'])) {
                $data['adv_payment'] = 0;
            }

            // Build the SQL query for payment
            $columns = implode(', ', array_keys($data));
            $placeholders = ':' . implode(', :', array_keys($data));

            $sql = "INSERT INTO payments ($columns) VALUES ($placeholders)";
            $stmt = $this->db->query($sql, $data);

            $paymentId = $this->db->connection->lastInsertId();

            // Update statement unpaid amount
            // First, get the statement to verify it exists and get current unpaid amount
            $sql = "SELECT * FROM statements WHERE statement_id = :statement_id AND company_id = :company_id LIMIT 1";
            $stmt = $this->db->query($sql, [
                'statement_id' => $data['statement_id'],
                'company_id' => $data['company_id']
            ]);
            $statement = $stmt->fetch();

            if (!$statement) {
                throw new Exception("Statement not found");
            }

            // Calculate new unpaid amount
            $paidAmount = floatval($data['paid_amount']);
            $unpaidAmount = floatval($statement['unpaid_amount']);
            $newUnpaidAmount = max(0, $unpaidAmount - $paidAmount);

            // Determine new status
            $status = 'Unpaid';
            if ($newUnpaidAmount <= 0) {
                $status = 'Paid';
            } else if ($newUnpaidAmount < $statement['total_amount']) {
                $status = 'Partially Paid';
            }

            // Check if overdue
            if ($newUnpaidAmount > 0 && strtotime($statement['due_date']) < time()) {
                $status = 'Overdue';
            }

            // Update statement unpaid amount and status
            $sql = "UPDATE statements 
                SET unpaid_amount = :unpaid_amount, 
                    status = :status, 
                    updated_at = :updated_at 
                WHERE statement_id = :statement_id 
                AND company_id = :company_id";

            $stmt = $this->db->query($sql, [
                'unpaid_amount' => $newUnpaidAmount,
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s'),
                'statement_id' => $data['statement_id'],
                'company_id' => $data['company_id']
            ]);

            if ($stmt->rowCount() === 0) {
                throw new Exception("Failed to update statement");
            }

            // Commit the transaction
            $this->db->connection->commit();

            return $paymentId;
        } catch (Exception $e) {
            // Rollback the transaction on error
            $this->db->connection->rollBack();
            error_log("Error creating payment: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Update payment status
     * 
     * @param int $id Payment ID
     * @param string $status New status
     * @param int $companyId Company ID (for security)
     * @return bool Success or failure
     */
    public function updateStatus($id, $status, $companyId)
    {
        $sql = "UPDATE payments 
                SET payment_status = :status, updated_at = :updated_at 
                WHERE payment_id = :id AND company_id = :company_id";

        try {
            $stmt = $this->db->query($sql, [
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s'),
                'id' => $id,
                'company_id' => $companyId
            ]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error updating payment status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get payments for a specific statement
     * 
     * @param int $statementId Statement ID
     * @param int $companyId Company ID (for security)
     * @return array Array of payments
     */
    public function getForStatement($statementId, $companyId)
    {
        $sql = "SELECT p.*, u.first_name as created_by_first_name, u.last_name as created_by_last_name 
                FROM payments p
                LEFT JOIN users u ON p.created_by = u.user_id
                WHERE p.statement_id = :statement_id AND p.company_id = :company_id 
                ORDER BY p.payment_date DESC, p.created_at DESC";

        $stmt = $this->db->query($sql, [
            'statement_id' => $statementId,
            'company_id' => $companyId
        ]);

        return $stmt->fetchAll();
    }

    /**
     * Get total payments for a specific statement
     * 
     * @param int $statementId Statement ID
     * @param int $companyId Company ID (for security)
     * @return float Total paid amount
     */
    public function getTotalPaidForStatement($statementId, $companyId)
    {
        $sql = "SELECT SUM(paid_amount) as total_paid FROM payments 
                WHERE statement_id = :statement_id AND company_id = :company_id 
                AND payment_status = 'Completed'";

        $stmt = $this->db->query($sql, [
            'statement_id' => $statementId,
            'company_id' => $companyId
        ]);
        $result = $stmt->fetch();

        return (float) ($result['total_paid'] ?? 0);
    }

    /**
     * Get payments for a specific subscriber
     * 
     * @param int $subscriberId Subscriber ID
     * @param int $companyId Company ID (for security)
     * @param int $limit Number of results to return
     * @return array Array of payments
     */
    public function getForSubscriber($subscriberId, $companyId, $limit = 10)
    {
        $sql = "SELECT p.*, s.statement_no 
                FROM payments p
                JOIN statements s ON p.statement_id = s.statement_id
                WHERE s.subscriber_id = :subscriber_id AND p.company_id = :company_id 
                ORDER BY p.payment_date DESC, p.created_at DESC
                LIMIT :limit";

        $stmt = $this->db->query($sql, [
            'subscriber_id' => $subscriberId,
            'company_id' => $companyId,
            'limit' => $limit
        ]);

        return $stmt->fetchAll();
    }

    /**
     * Count payments for a specific subscriber
     * 
     * @param int $subscriberId Subscriber ID
     * @param int $companyId Company ID (for security)
     * @return int Count of payments
     */
    public function countForSubscriber($subscriberId, $companyId)
    {
        $sql = "SELECT COUNT(*) as count FROM payments p
                JOIN statements s ON p.statement_id = s.statement_id
                WHERE s.subscriber_id = :subscriber_id AND p.company_id = :company_id";

        $stmt = $this->db->query($sql, [
            'subscriber_id' => $subscriberId,
            'company_id' => $companyId
        ]);
        $result = $stmt->fetch();

        return (int) $result['count'];
    }
}

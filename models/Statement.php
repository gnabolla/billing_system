<?php

class Statement
{
    protected $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    /**
     * Get all statements for a company
     * 
     * @param int $companyId The company ID
     * @param array $filters Optional filters (status, search, etc.)
     * @param int $limit Number of results per page
     * @param int $offset Offset for pagination
     * @return array Array of statements
     */
    public function getAllByCompany($companyId, $filters = [], $limit = 50, $offset = 0)
    {
        $params = ['company_id' => $companyId];
        $whereConditions = ['s.company_id = :company_id'];

        // Apply filters if provided
        if (!empty($filters['status'])) {
            $whereConditions[] = 's.status = :status';
            $params['status'] = $filters['status'];
        }

        // Filter by subscriber if provided
        if (!empty($filters['subscriber_id'])) {
            $whereConditions[] = 's.subscriber_id = :subscriber_id';
            $params['subscriber_id'] = $filters['subscriber_id'];
        }

        // Search by statement number or subscriber info
        if (!empty($filters['search'])) {
            $whereConditions[] = '(s.statement_no LIKE :search OR 
                                  sub.account_no LIKE :search OR 
                                  sub.company_name LIKE :search OR 
                                  CONCAT(sub.first_name, " ", sub.last_name) LIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
        }

        // Filter by date range
        if (!empty($filters['date_from'])) {
            $whereConditions[] = 's.bill_period_start >= :date_from';
            $params['date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $whereConditions[] = 's.bill_period_end <= :date_to';
            $params['date_to'] = $filters['date_to'];
        }

        $whereClause = implode(' AND ', $whereConditions);

        $sql = "SELECT s.*, 
                sub.account_no, sub.company_name, sub.first_name, sub.last_name
                FROM statements s
                JOIN subscribers sub ON s.subscriber_id = sub.subscriber_id
                WHERE $whereClause 
                ORDER BY s.created_at DESC 
                LIMIT " . (int)$limit . " OFFSET " . (int)$offset;

        $stmt = $this->db->query($sql, $params);

        return $stmt->fetchAll();
    }

    /**
     * Count total statements for a company
     * 
     * @param int $companyId The company ID
     * @param array $filters Optional filters
     * @return int Total count
     */
    public function countByCompany($companyId, $filters = [])
    {
        $params = ['company_id' => $companyId];
        $whereConditions = ['s.company_id = :company_id'];

        // Apply filters if provided
        if (!empty($filters['status'])) {
            $whereConditions[] = 's.status = :status';
            $params['status'] = $filters['status'];
        }

        // Filter by subscriber if provided
        if (!empty($filters['subscriber_id'])) {
            $whereConditions[] = 's.subscriber_id = :subscriber_id';
            $params['subscriber_id'] = $filters['subscriber_id'];
        }

        // Search by statement number or subscriber info
        if (!empty($filters['search'])) {
            $whereConditions[] = '(s.statement_no LIKE :search OR 
                                  sub.account_no LIKE :search OR 
                                  sub.company_name LIKE :search OR 
                                  CONCAT(sub.first_name, " ", sub.last_name) LIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
        }

        // Filter by date range
        if (!empty($filters['date_from'])) {
            $whereConditions[] = 's.bill_period_start >= :date_from';
            $params['date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $whereConditions[] = 's.bill_period_end <= :date_to';
            $params['date_to'] = $filters['date_to'];
        }

        $whereClause = implode(' AND ', $whereConditions);

        $sql = "SELECT COUNT(*) as count 
                FROM statements s
                JOIN subscribers sub ON s.subscriber_id = sub.subscriber_id
                WHERE $whereClause";

        $stmt = $this->db->query($sql, $params);
        $result = $stmt->fetch();

        return (int) $result['count'];
    }

    /**
     * Get statement by ID
     * 
     * @param int $id Statement ID
     * @param int $companyId Company ID (for security)
     * @return array|bool Statement data or false if not found
     */
    public function getById($id, $companyId)
    {
        $sql = "SELECT s.*, 
                sub.account_no, sub.company_name, sub.first_name, sub.last_name, 
                sub.address, sub.phone_number, sub.email
                FROM statements s
                JOIN subscribers sub ON s.subscriber_id = sub.subscriber_id
                WHERE s.statement_id = :id AND s.company_id = :company_id 
                LIMIT 1";

        $stmt = $this->db->query($sql, [
            'id' => $id,
            'company_id' => $companyId
        ]);

        return $stmt->fetch() ?: false;
    }

    /**
     * Get all items for a statement
     * 
     * @param int $statementId Statement ID
     * @return array Statement items
     */
    public function getItems($statementId)
    {
        $sql = "SELECT * FROM statement_items 
                WHERE statement_id = :statement_id 
                ORDER BY item_id ASC";

        $stmt = $this->db->query($sql, ['statement_id' => $statementId]);

        return $stmt->fetchAll();
    }

    /**
     * Generate a unique statement number
     * 
     * @param int $companyId Company ID
     * @return string Unique statement number
     */
    public function generateStatementNumber($companyId)
    {
        // Get current year and month
        $year = date('Y');
        $month = date('m');

        // Get count of statements for this company in current month/year
        $sql = "SELECT COUNT(*) as count FROM statements 
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

        // Format: INV-YYYYMM-CompanyID-SequentialNumber (padded to 4 digits)
        return 'INV-' . $year . $month . '-' . $companyId . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Create a new statement
     * 
     * @param array $data Statement data
     * @return int|bool The ID of the new statement or false on failure
     */
    public function create($data)
    {
        // Begin transaction
        $this->db->connection->beginTransaction();

        try {
            // Set timestamps
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');

            // If statement_no is not provided, generate one
            if (empty($data['statement_no'])) {
                $data['statement_no'] = $this->generateStatementNumber($data['company_id']);
            }

            // Prepare statement data (exclude items)
            $statementData = array_filter($data, function ($key) {
                return $key !== 'items';
            }, ARRAY_FILTER_USE_KEY);

            // Build the SQL query for statement
            $columns = implode(', ', array_keys($statementData));
            $placeholders = ':' . implode(', :', array_keys($statementData));

            $sql = "INSERT INTO statements ($columns) VALUES ($placeholders)";
            $stmt = $this->db->query($sql, $statementData);

            $statementId = $this->db->connection->lastInsertId();

            // Insert statement items if provided
            if (!empty($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    $item['statement_id'] = $statementId;
                    $item['created_at'] = date('Y-m-d H:i:s');
                    $item['updated_at'] = date('Y-m-d H:i:s');

                    $itemColumns = implode(', ', array_keys($item));
                    $itemPlaceholders = ':' . implode(', :', array_keys($item));

                    $itemSql = "INSERT INTO statement_items ($itemColumns) VALUES ($itemPlaceholders)";
                    $this->db->query($itemSql, $item);
                }
            }

            // Commit the transaction
            $this->db->connection->commit();

            return $statementId;
        } catch (PDOException $e) {
            // Rollback the transaction on error
            $this->db->connection->rollBack();
            error_log("Error creating statement: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update statement status
     * 
     * @param int $id Statement ID
     * @param string $status New status
     * @param int $companyId Company ID (for security)
     * @return bool Success or failure
     */
    public function updateStatus($id, $status, $companyId)
    {
        $sql = "UPDATE statements 
                SET status = :status, updated_at = :updated_at 
                WHERE statement_id = :id AND company_id = :company_id";

        try {
            $stmt = $this->db->query($sql, [
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s'),
                'id' => $id,
                'company_id' => $companyId
            ]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error updating statement status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update statement unpaid amount
     * 
     * @param int $id Statement ID
     * @param float $newUnpaidAmount New unpaid amount
     * @param int $companyId Company ID (for security)
     * @return bool Success or failure
     */
    public function updateUnpaidAmount($id, $newUnpaidAmount, $companyId)
    {
        // Start transaction
        $this->db->connection->beginTransaction();

        try {
            // First verify statement exists and belongs to this company
            $sql = "SELECT * FROM statements WHERE statement_id = :id AND company_id = :company_id LIMIT 1";
            $stmt = $this->db->query($sql, [
                'id' => $id,
                'company_id' => $companyId
            ]);

            $statement = $stmt->fetch();
            if (!$statement) {
                error_log("Statement not found for ID: $id and company ID: $companyId");
                throw new Exception("Statement not found");
            }

            // Update the unpaid amount
            $sql = "UPDATE statements 
                SET unpaid_amount = :unpaid_amount, updated_at = :updated_at 
                WHERE statement_id = :id AND company_id = :company_id";

            $params = [
                'unpaid_amount' => $newUnpaidAmount,
                'updated_at' => date('Y-m-d H:i:s'),
                'id' => $id,
                'company_id' => $companyId
            ];

            $stmt = $this->db->query($sql, $params);

            if ($stmt->rowCount() === 0) {
                error_log("No rows updated when updating unpaid amount for statement ID: $id");
                throw new Exception("Failed to update statement. No matching record found.");
            }

            // Determine the new status based on the unpaid amount
            $status = 'Unpaid';
            if ($newUnpaidAmount <= 0) {
                $status = 'Paid';
            } else {
                $totalAmount = $statement['total_amount'];
                if ($newUnpaidAmount < $totalAmount) {
                    $status = 'Partially Paid';
                }

                // Check if overdue (due date has passed and still unpaid)
                if (strtotime($statement['due_date']) < time()) {
                    $status = 'Overdue';
                }
            }

            // Update the status
            $sql = "UPDATE statements 
                SET status = :status, updated_at = :updated_at 
                WHERE statement_id = :id AND company_id = :company_id";

            $stmt = $this->db->query($sql, [
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s'),
                'id' => $id,
                'company_id' => $companyId
            ]);

            // Commit transaction
            $this->db->connection->commit();

            return true;
        } catch (PDOException $e) {
            // Rollback transaction on error
            $this->db->connection->rollBack();
            error_log("Error updating unpaid amount: " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->db->connection->rollBack();
            error_log("Error in update process: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get total amount for a statement
     * 
     * @param int $id Statement ID
     * @return float Total amount
     */
    private function getTotalAmount($id)
    {
        $sql = "SELECT total_amount FROM statements WHERE statement_id = :id LIMIT 1";
        $stmt = $this->db->query($sql, ['id' => $id]);
        $result = $stmt->fetch();

        return $result ? (float) $result['total_amount'] : 0;
    }

    /**
     * Get statements for a specific subscriber
     * 
     * @param int $subscriberId Subscriber ID
     * @param int $companyId Company ID (for security)
     * @param int $limit Number of results to return
     * @return array Array of statements
     */
    public function getForSubscriber($subscriberId, $companyId, $limit = 10)
    {
        $sql = "SELECT * FROM statements 
                WHERE subscriber_id = :subscriber_id AND company_id = :company_id 
                ORDER BY created_at DESC LIMIT :limit";

        $stmt = $this->db->query($sql, [
            'subscriber_id' => $subscriberId,
            'company_id' => $companyId,
            'limit' => $limit
        ]);

        return $stmt->fetchAll();
    }

    /**
     * Count statements for a specific subscriber
     * 
     * @param int $subscriberId Subscriber ID
     * @param int $companyId Company ID (for security)
     * @return int Count of statements
     */
    public function countForSubscriber($subscriberId, $companyId)
    {
        $sql = "SELECT COUNT(*) as count FROM statements 
                WHERE subscriber_id = :subscriber_id AND company_id = :company_id";

        $stmt = $this->db->query($sql, [
            'subscriber_id' => $subscriberId,
            'company_id' => $companyId
        ]);
        $result = $stmt->fetch();

        return (int) $result['count'];
    }
}

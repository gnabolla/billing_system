<?php

class StatementItem
{
    protected $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    /**
     * Create a new statement item
     * 
     * @param array $data Item data
     * @return int|bool The ID of the new item or false on failure
     */
    public function create($data)
    {
        // Set timestamps
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        // Build the SQL query
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO statement_items ($columns) VALUES ($placeholders)";

        try {
            $stmt = $this->db->query($sql, $data);
            return $this->db->connection->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error creating statement item: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all items for a statement
     * 
     * @param int $statementId Statement ID
     * @return array Statement items
     */
    public function getAllForStatement($statementId)
    {
        $sql = "SELECT * FROM statement_items 
                WHERE statement_id = :statement_id 
                ORDER BY item_id ASC";

        $stmt = $this->db->query($sql, ['statement_id' => $statementId]);

        return $stmt->fetchAll();
    }

    /**
     * Delete an item
     * 
     * @param int $itemId Item ID
     * @return bool Success or failure
     */
    public function delete($itemId)
    {
        $sql = "DELETE FROM statement_items WHERE item_id = :item_id";

        try {
            $stmt = $this->db->query($sql, ['item_id' => $itemId]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error deleting statement item: " . $e->getMessage());
            return false;
        }
    }
}
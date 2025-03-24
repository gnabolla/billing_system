<?php

/**
 * Class Database
 *
 * Simple Database wrapper using PDO.
 */
class Database
{
    /** @var PDO */
    public $connection;

    /**
     * Database constructor.
     *
     * @param array $config Array of config details (host, port, dbname, charset, username, password).
     */
    public function __construct(array $config)
    {
        // Build DSN string. Example:
        // mysql:host=localhost;port=3306;dbname=billing_db;charset=utf8mb4
        $dsn = 'mysql:' . http_build_query($config, '', ';');

        // Pull username and password directly from $config
        $username = $config['username'] ?? 'root';
        $password = $config['password'] ?? '';

        // Create the PDO connection
        $this->connection = new PDO($dsn, $username, $password, [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    /**
     * Prepare, execute, and return statement for a query.
     *
     * @param string $query  SQL query string.
     * @param array  $params Parameters to bind.
     *
     * @return PDOStatement
     */
    public function query(string $query, array $params = []): PDOStatement
    {
        $statement = $this->connection->prepare($query);
        $statement->execute($params);

        return $statement;
    }
}

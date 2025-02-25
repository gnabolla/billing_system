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
     * @param array  $config   Array of config details (host, port, dbname, charset).
     * @param string $username DB username.
     * @param string $password DB password.
     */
    public function __construct(array $config, string $username = 'root', string $password = '')
    {
        // Build DSN string. Example: mysql:host=localhost;port=3306;dbname=isur_system;charset=utf8mb4
        $dsn = 'mysql:' . http_build_query($config, '', ';');

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

<?php

namespace Database;

use Exception;
use mysqli;

/**
 * The class works with the database
 */
class DBHandler
{
    protected mysqli $connection;

    private string $migrationsDir = __DIR__ . '/migrations';

    public function __construct()
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $config = require('config.php');

        $this->connection = new mysqli(
            $config['database_host'],
            $config['database_username'],
            $config['database_password'],
        );

        /** If the database name does not exist in the environment variables, creates and selects it */
        if (!$config['database_name']) {
            $this->connection->query("create database if not exists `books`;");
            $this->connection->select_db('books');
        } else {
            $this->connection->select_db($config['database_name']);
        }
    }

    public function __destruct()
    {
        $this->connection->close();
    }

    /**
     * Allows you to execute a manually written SQL query
     *
     * @param  string  $query
     * @return array|mixed
     */
    public function raw(string $query)
    {
        return $this->connection->query($query)->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * The method create and execute a query for insert
     *
     * @param  string  $table  Name of the table into which the data are added
     * @param  array  $data  An associative array of data to insert, where the key is the column name
     * @return int|bool Returns id of inserted row and false on failure
     */
    public function insert(string $table, array $data): int
    {
        $columns = array_keys($data);
        $values = array_values($data);

        $query = "insert into $table (";
        $query .= implode(', ', $columns);
        $query .= ') values (';
        $query .= implode(', ', $this->prepareValues($values));
        $query .= ');';

        try {
            $this->connection->begin_transaction();
            $this->connection->query($query);
            $insertedId = $this->connection->insert_id;
            $this->connection->commit();

            return $insertedId;
        } catch (Exception $exception) {
            $this->connection->rollback();
        }

        return false;
    }

    /**
     * The method prepares the array of data in the desired format for insertion
     * @param  array  $data  Array of data to be prepared
     * @return array Returns array with prepared data
     */
    protected function prepareValues(array $data): array
    {
        return array_map(function ($item) {
            return $this->prepareValue($item);
        }, $data);
    }

    /**
     * The method prepares the value in the desired format for insertion
     *
     * @param  mixed  $value  The value to be prepared
     * @return string
     */
    protected function prepareValue($value): string
    {
        if ($value || is_int($value)) {
            if (is_array($value)) {
                $value = json_encode($value);
            }

            if ($value === 'NOW()') {
                return $value;
            }

            return '\'' . addslashes($value) . '\'';
        }

        return 'null';
    }

    /**
     * The method creates tables that are stored in the migration directory
     *
     * @return void
     */
    public function createTables(): void
    {
        foreach ($this->getMigrations() as $migration) {
            $this->connection->query((require($this->migrationsDir . '/' . $migration))->buildQuery());
        }
    }

    /**
     * Parses migrations directory
     *
     * @return array|false
     */
    protected function getMigrations()
    {
        $files = scandir($this->migrationsDir);

        return array_filter(
            $files,
            static fn($file) => !is_dir($file) && (pathinfo($file, PATHINFO_EXTENSION) === 'php')
        );
    }
}
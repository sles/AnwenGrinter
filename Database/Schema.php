<?php

namespace Database;

use InvalidArgumentException;

/**
 * A class that builds a query to create a table
 */
class Schema
{
    /**
     * Name of the table to be created
     *
     * @var string
     */
    public string $table;

    /**
     * An array that contains columns that need to be added to the table
     *
     * @var array
     */
    protected array $columns;

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    /**
     * Adds a primary key column
     *
     * @param  string  $name  Name of the column
     * @return BaseColumnBuilder
     */
    public function id(string $name = 'id'): BaseColumnBuilder
    {
        $column = new BaseColumnBuilder($name, 'int unsigned');
        $column->primaryKey()->notNull()->autoIncrement();

        $this->columns[] = $column;

        return $column;
    }

    /**
     * Adds a column with varchar type
     *
     * @param  string  $name  Name of the column
     * @param  int  $size  Maximum number of bytes reserved for that column
     * @return BaseColumnBuilder
     */
    public function string(string $name, int $size = 255): BaseColumnBuilder
    {
        if ($size < 1) {
            throw new InvalidArgumentException('Argument "size" can\'t be smaller then 1');
        }

        $column = new BaseColumnBuilder($name, "varchar($size)");

        $this->columns[] = $column;

        return $column;
    }

    /**
     * Adds two columns (created_at, updated_at), which specifies the date the record was created and updated
     *
     * @return $this
     */
    public function timestamps(): Schema
    {
        $created_at = new BaseColumnBuilder('created_at', 'datetime');
        $created_at->notNull();

        $updated_at = new BaseColumnBuilder('updated_at', 'datetime');
        $updated_at->notNull();

        $this->columns[] = $created_at;
        $this->columns[] = $updated_at;

        return $this;
    }

    /**
     * Adds a column with integer type
     *
     * @param  string  $name  Name of the column
     * @param  bool  $unsigned  Specifies whether to create an unsigned int type
     * @return BaseColumnBuilder
     */
    public function int(string $name, bool $unsigned = false): BaseColumnBuilder
    {
        $column = new BaseColumnBuilder($name, 'integer' . ($unsigned ? ' unsigned' : ''));

        $this->columns[] = $column;

        return $column;
    }

    /**
     * Adds a column with datetime type
     *
     * @param  string  $name  Name of the column
     * @return BaseColumnBuilder
     */
    public function datetime(string $name): BaseColumnBuilder
    {
        $column = new BaseColumnBuilder($name, 'datetime');

        $this->columns[] = $column;

        return $column;
    }

    /**
     * Adds a compound primary key based on the provided array of column names
     *
     * @param  string[]  $columns  Array of column names to be composited into a primary key
     * @return $this
     */
    public function primaryKey(array $columns): Schema
    {
        $this->columns[] = 'primary key (' . (implode(', ', $columns)) . ')';

        return $this;
    }

    /**
     * Adds a foreign key constraint
     *
     * @param  string  $columnName  Name of the column
     * @return ForeignKeyColumnBuilder
     */
    public function foreignKey(string $columnName): ForeignKeyColumnBuilder
    {
        $column = new ForeignKeyColumnBuilder($columnName, $this->table);

        $this->columns[] = $column;

        return $column;
    }

    /**
     * Builds an SQL query based on an array of columns
     *
     * @return string Returns the built SQL query
     */
    public function buildQuery(): string
    {
        $columnQueries = [];

        foreach ($this->columns as $column) {
            if ($column instanceof BaseColumnBuilder || $column instanceof ForeignKeyColumnBuilder) {
                $columnQueries[] = $column->create();
            } else {
                $columnQueries[] = $column;
            }
        }

        $query = "create table if not exists $this->table (";
        $query .= implode(',', $columnQueries);
        $query .= ');';

        return $query;
    }
}
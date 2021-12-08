<?php

namespace Database;

/**
 * The class constructs the foreign key constraint
 */
class ForeignKeyColumnBuilder
{
    /**
     * Name of the column
     *
     * @var string
     */
    private string $columnName;

    /**
     * Name of the table to which the foreign key refers
     *
     * @var string
     */
    private string $referenceTable;

    /**
     * Name of the column to which the foreign key refers
     *
     * @var string
     */
    private string $referenceOnColumn;

    /**
     * Indicates what to do if the row in the parent table has been deleted
     *
     * @var string|null
     */
    private ?string $onDelete = null;

    /**
     * Indicates what to do if the row in the parent table has been updated
     *
     * @var string|null
     */
    private ?string $onUpdate = null;

    /**
     * Name of the table from which the relation is created
     *
     * @var string
     */
    private string $table;

    public function __construct(string $columnName, string $table)
    {
        $this->columnName = $columnName;
        $this->table = $table;
    }

    /**
     * Specifies the name of the table referenced by the relation
     *
     * @param  string  $table  Name of the table
     * @return $this
     */
    public function references(string $table): ForeignKeyColumnBuilder
    {
        $this->referenceTable = $table;

        return $this;
    }

    /**
     * Specifies the name of the column referenced by the relation
     *
     * @param  string  $column
     * @return $this
     */
    public function on(string $column): ForeignKeyColumnBuilder
    {
        $this->referenceOnColumn = $column;

        return $this;
    }

    /**
     * Adds a rule specifying what to do if a row in the parent table has been deleted
     *
     * @param  string  $value
     * @return $this
     */
    public function onDelete(string $value): ForeignKeyColumnBuilder
    {
        $this->onDelete = $value;

        return $this;
    }

    /**
     * Adds a rule specifying what to do if a row in the parent table has been updated
     *
     * @param  string  $value
     * @return $this
     */
    public function onUpdate(string $value): ForeignKeyColumnBuilder
    {
        $this->onUpdate = $value;

        return $this;
    }

    /**
     * Creates an SQL query to add a foreign key constraint
     *
     * @return string
     */
    public function create(): string
    {
        $query = "constraint `fk_{$this->table}_{$this->columnName}_{$this->referenceTable}_$this->referenceOnColumn` ";
        $query .= "foreign key (`$this->columnName`) references `$this->referenceTable`(`$this->referenceOnColumn`)";

        if ($this->onDelete) {
            $query .= " on delete $this->onDelete";
        }

        if ($this->onUpdate) {
            $query .= " on update $this->onDelete";
        }

        return $query;
    }
}
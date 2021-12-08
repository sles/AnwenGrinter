<?php

namespace Database;

/**
 * A class that builds a query to add a column to the database
 */
class BaseColumnBuilder
{
    /**
     * Column type
     *
     * @var string
     */
    private string $type;

    /**
     * Column name
     *
     * @var string
     */
    private string $name;

    /**
     * Array of column modifiers
     * @var array
     */
    private array $modifiers = [];

    public function __construct(string $name, string $type)
    {
        $this->type = $type;
        $this->name = $name;
    }

    /**
     * Adding the "auto_increment" modifier
     *
     * @return $this
     */
    public function autoIncrement(): BaseColumnBuilder
    {
        $this->modifiers[] = 'auto_increment';

        return $this;
    }

    /**
     * Adding the "not null" modifier
     *
     * @return $this
     */
    public function notNull(): BaseColumnBuilder
    {
        $this->modifiers[] = 'not null';

        return $this;
    }

    /**
     * Adding the "primary key" modifier
     *
     * @return $this
     */
    public function primaryKey(): BaseColumnBuilder
    {
        $this->modifiers[] = 'primary key';

        return $this;
    }

    /**
     * Adding the "default" modifier
     *
     * @param  mixed  $value  Default value
     * @return BaseColumnBuilder
     */
    public function default($value): BaseColumnBuilder
    {
        $this->modifiers[] = "default('$value')";

        return $this;
    }

    /**
     * Adding the "unique" modifier
     *
     * @return $this
     */
    public function unique(): BaseColumnBuilder
    {
        $this->modifiers[] = 'unique';

        return $this;
    }

    /**
     * Builds an SQL query based on an array of column modifiers
     *
     * @return string Returns SQL query
     */
    public function create(): string
    {
        $query = "`$this->name` $this->type ";
        $query .= implode(' ', $this->modifiers);

        return $query;
    }
}
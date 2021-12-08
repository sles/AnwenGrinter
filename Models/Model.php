<?php

namespace Models;

use Database\DBHandler;
use Database\SelectQueryBuilder;

/**
 * A base class for working with data from the database
 */
class Model
{
    /**
     * The name of the table that contains the data related to this class.
     * By default, it is a lowercase class name with 's' prefix
     *
     * @var string
     */
    protected string $table = '';

    /**
     * An array that stores database record data, where the key is the name of the column and the value is its value.
     *
     * @var array
     */
    protected array $attributes;

    /**
     * Determines whether there are timestamps in the model
     *
     * @var bool
     */
    protected bool $timestamps = true;

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }


    /**
     * Return table name for this model
     *
     * @return string
     */
    public function getTable(): string
    {
        $classNameParts = explode("\\", strtolower(get_class($this)));

        return $this->table ?: end($classNameParts) . 's';
    }

    /**
     * Filling model attributes array by provided data array
     *
     * @param  array  $data
     * @return Model
     */
    public function fill(array $data): Model
    {
        $this->attributes = $data;

        return $this;
    }

    /**
     * Returns the value taken from the array of attributes
     *
     * @param  string  $key  Attribute name
     * @return mixed|null
     */
    public function getAttribute(string $key)
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public static function query(): SelectQueryBuilder
    {
        $model = new static();

        return new SelectQueryBuilder($model->getTable(), static::class);
    }

    /**
     * Adding a record to the database using the provided data array,
     * where key is the column name and value is its value
     *
     * @param  array  $data
     * @return Model
     */
    public static function create(array $data): Model
    {
        /** Creating a new model instance */
        $model = new static();

        /** Creating a connection with database */
        $dbh = new DBHandler();

        /** Checking whether timestamps should be used  */
        if ($model->timestamps) {
            $now = date_create()->format('Y-m-d H:i:s');
            $data = array_merge($data, [
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }

        /** Inserting a record and getting its id */
        $id = $dbh->insert($model->getTable(), $data);

        /** Adding "id" column to array of attributes */
        $model->attributes = array_merge(['id' => $id], $data);

        return $model;
    }
}
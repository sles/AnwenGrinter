<?php

namespace Database;

use Models\Model;

class SelectQueryBuilder
{
    private string $table;
    private array $query;

    /**
     * @var string|Model|null
     */
    private $model;

    public function __construct(string $table, $model = null)
    {
        $this->table = $table;

        $this->query = [
            'where' => [],
            'select' => [],
            'join' => []
        ];

        $this->model = $model;
    }

    public function where($column, $value, $operator = '='): SelectQueryBuilder
    {
        if ($value instanceof self) {
            $this->query['where'][] = "$column in ({$value->buildQuery()})";

            return $this;
        }

        $this->query['where'][] = "$column $operator '$value'";

        return $this;
    }

    public function join($table, $fromColumn, $toColumn, $type = 'inner', $operator = '='): SelectQueryBuilder
    {
        $this->query['join'][] = "$type join $table on $fromColumn $operator $toColumn";

        return $this;
    }

    public function addSelect(array $columns): SelectQueryBuilder
    {
        $this->query['select'] = array_merge($this->query['select'], $columns);

        return $this;
    }

    public function orderBy(string $column, $order = 'asc'): SelectQueryBuilder
    {
        $this->query['order'][] = "order by $column $order";

        return $this;
    }

    public function groupBy(string $column): SelectQueryBuilder
    {
        $this->query['groupBy'][] = "group by $column";

        return $this;
    }

    public function limit(int $limit): SelectQueryBuilder
    {
        $this->query['limit'] = "limit $limit";

        return $this;
    }

    public function buildQuery(): string
    {
        if (empty($this->query['select'])) {
            $this->query['select'][] = '*';
        }

        $query = 'select ' . implode(', ', $this->query['select']) . " from $this->table ";

        if (!empty($this->query['join'])) {
            $query .= implode(' ', $this->query['join']) . ' ';
        }

        if (!empty($this->query['where'])) {
            $query .= 'where ' . implode(' AND ', $this->query['where']) . ' ';
        }

        if (!empty($this->query['groupBy'])) {
            $query .= implode(' ', $this->query['groupBy']) . ' ';
        }

        if (!empty($this->query['order'])) {
            $query .= implode(' ', $this->query['order']) . ' ';
        }

        if (!empty($this->query['limit'])) {
            $query .= $this->query['limit'];
        }

        return $query;
    }

    /**
     * Returns array of models if "model" argument has passed, otherwise returns array of fetched data
     *
     * @return Model[]|array
     */
    public function get(): array
    {
        $data = (new DBHandler())->raw($this->buildQuery());

        if ($this->model) {
            return array_map(fn($item) => new $this->model($item), $data);
        }

        return $data;
    }
}
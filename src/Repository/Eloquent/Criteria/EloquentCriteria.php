<?php

namespace Hypocenter\LaravelVitamin\Repository\Eloquent;


use Hypocenter\LaravelVitamin\Repository\Contracts\Criteria;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class EloquentCriteria implements Criteria
{
    protected $searchable;

    protected $searches;

    protected $prefix;

    public function set($searches)
    {
        $this->searches = $searches;
        return $this;
    }

    public function setSearchable(array $searchable = null)
    {
        $this->searchable = (array)$searchable;
        return $this;
    }

    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * @param $keys string|array
     *
     * @return $this|static
     */
    public function only($keys)
    {
        $this->searchable = Arr::only($this->searchable, $keys);
        return $this;
    }

    /**s
     *
     * @param $keys string|array
     *
     * @return $this|static
     */
    public function except($keys)
    {
        $this->searchable = Arr::only($this->searchable, $keys);
        return $this;
    }

    /**
     * @param Query $query
     */
    public function apply($query)
    {
        $searches = $this->searches;

        foreach ($this->searchable as $field => $type) {

            if (is_numeric($field)) {
                $field = $type;
                $type  = null;
            }

            if (!isset($searches[$field])) {
                continue;
            }

            if (is_string($type)) {
                $type = [$type];
            }

            $this->search($query, $field, $searches[$field], $type);
        }
    }

    /**
     * @param  Query   $query
     * @param          $field
     * @param null     $value
     * @param null     $type
     */
    protected function search($query, $field, $value = null, $type = null)
    {
        $operator = static::OP_EQ;

        if (isset($type[0])) {
            $operator = $type[0];
        }

        if (isset($type['map'])) {
            $field = $type['map'];
        }

        if ($operator === static::OP_EQ) {
            $query->where($this->getField($query, $field), $value);
            return;
        }

        if ($operator === static::OP_LIKE) {
            $query->where($this->getField($query, $field), 'like', "%$value%");
            return;
        }

        if ($operator === static::OP_GT) {
            $query->where($this->getField($query, $field), '>', $value);
            return;
        }

        if ($operator === static::OP_GTE) {
            $query->where($this->getField($query, $field), '>=', $value);
            return;
        }

        if ($operator === static::OP_LT) {
            $query->where($this->getField($query, $field), '<', $value);
            return;
        }

        if ($operator === static::OP_LTE) {
            $query->where($this->getField($query, $field), '<=', $value);
            return;
        }

        if ($operator === static::OP_IN) {
            $query->whereIn($this->getField($query, $field), Arr::wrap($value));
            return;
        }

        if ($operator === static::OP_START_WITH) {
            $query->where($this->getField($query, $field), 'like', "$value%");
            return;
        }

        if ($operator === static::OP_END_WITH) {
            $query->where($this->getField($query, $field), 'like', "%$value");
            return;
        }

        if ($operator === static::OP_CUSTOM) {
            $method = 'custom' . ucfirst(Str::camel($field));
            if (method_exists($this, $method)) {
                $this->$method($query, $value);
            }
            return;
        }
    }

    protected function getField(Query $builder, $field)
    {
        if (Str::contains($field, '.')) {
            return $field;
        }

        if ($this->prefix) {
            return "{$this->prefix}.$field";
        }

        return $builder->qualifyColumn($field);
    }
}
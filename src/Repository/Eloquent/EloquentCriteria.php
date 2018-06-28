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
     * @param Builder $builder
     */
    public function apply($builder)
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

            $this->search($builder, $field, $searches[$field], $type);
        }
    }

    /**
     * @param  Builder $builder
     * @param          $field
     * @param null     $value
     * @param null     $type
     */
    protected function search($builder, $field, $value = null, $type = null)
    {
        $operator = static::OP_EQ;

        if (isset($type[0])) {
            $operator = $type[0];
        }

        if (isset($type['map'])) {
            $field = $type['map'];
        }

        if ($operator === static::OP_EQ) {
            $builder->where($this->getField($builder, $field), $value);
            return;
        }

        if ($operator === static::OP_LIKE) {
            $builder->where($this->getField($builder, $field), 'like', "%$value%");
            return;
        }

        if ($operator === static::OP_GT) {
            $builder->where($this->getField($builder, $field), '>', $value);
            return;
        }

        if ($operator === static::OP_GTE) {
            $builder->where($this->getField($builder, $field), '>=', $value);
            return;
        }

        if ($operator === static::OP_LT) {
            $builder->where($this->getField($builder, $field), '<', $value);
            return;
        }

        if ($operator === static::OP_LTE) {
            $builder->where($this->getField($builder, $field), '<=', $value);
            return;
        }

        if ($operator === static::OP_IN) {
            $builder->whereIn($this->getField($builder, $field), Arr::wrap($value));
            return;
        }

        if ($operator === static::OP_START_WITH) {
            $builder->where($this->getField($builder, $field), 'like', "$value%");
            return;
        }

        if ($operator === static::OP_END_WITH) {
            $builder->where($this->getField($builder, $field), 'like', "%$value");
            return;
        }

        if ($operator === static::OP_CUSTOM) {
            $method = 'custom' . ucfirst(Str::camel($field));
            if (method_exists($this, $method)) {
                $this->$method($builder, $value);
            }
            return;
        }
    }

    protected function getField(Builder $builder, $field)
    {
        if ($this->prefix) {
            return "{$this->prefix}.$field";
        }

        if (count((array) $builder->getQuery()->joins) > 0) {
            return $builder->getModel()->qualifyColumn($field);
        }

        return $field;
    }
}
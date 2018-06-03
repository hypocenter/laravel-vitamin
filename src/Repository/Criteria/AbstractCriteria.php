<?php

namespace Hypocenter\LaravelVitamin\Repository\Criteria;


use Hypocenter\LaravelVitamin\Repository\Contracts\Criteria;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

abstract class AbstractCriteria implements Criteria
{
    protected $searchable;

    abstract protected function receive();

    public function setSearchable(array $searchable = null)
    {
        $this->searchable = (array)$searchable;
        return $this;
    }

    /**
     * @param Builder $builder
     */
    public function apply($builder)
    {
        $searches = $this->receive();

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
            $builder->where($field, $value);
            return;
        }

        if ($operator === static::OP_LIKE) {
            $builder->where($field, 'like', "%$value%");
            return;
        }

        if ($operator === static::OP_GT) {
            $builder->where($field, '>', $value);
            return;
        }

        if ($operator === static::OP_GTE) {
            $builder->where($field, '>=', $value);
            return;
        }

        if ($operator === static::OP_LT) {
            $builder->where($field, '<', $value);
            return;
        }

        if ($operator === static::OP_LTE) {
            $builder->where($field, '<=', $value);
            return;
        }

        if ($operator === static::OP_IN) {
            $builder->whereIn($field, Arr::wrap($value));
        }
    }
}
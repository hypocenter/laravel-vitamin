<?php

namespace Hypocenter\LaravelVitamin\Repository\Criteria;


use Hypocenter\LaravelVitamin\Repository\Contracts\Criteria;
use Hypocenter\LaravelVitamin\Repository\Contracts\CriteriaParser;
use Illuminate\Database\Eloquent\Builder;

abstract class AbstractCriteria implements Criteria
{
    const OP_EQ   = 'eq';
    const OP_LIKE = 'like';
    const OP_GT   = 'gt';
    const OP_GTE  = 'gte';
    const OP_LT   = 'lt';
    const OP_LTE  = 'lte';

    protected $searchable;
    /**
     * @var CriteriaParser
     */
    protected $parser;

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
        $searches = $this->parse($this->receive());

        foreach ($this->searchable as $field => $type) {

            if (is_numeric($field)) {
                $field = $type;
                $type  = null;
            }

            if (!$searches->has($field)) {
                continue;
            }

            if (is_string($type)) {
                $type = [$type];
            }

            $this->search($builder, $field, $searches[$field], $type);
        }
    }

    protected function parse($value)
    {
        return resolve(CriteriaParser::class)->parse($value);
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
    }
}
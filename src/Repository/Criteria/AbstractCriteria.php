<?php

namespace Hypocenter\LaravelVitamin\Repository\Criteria;


use Hypocenter\LaravelVitamin\Repository\Contracts\Criteria;
use Hypocenter\LaravelVitamin\Repository\Contracts\CriteriaParser;
use Illuminate\Database\Eloquent\Builder;

abstract class AbstractCriteria implements Criteria
{
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

            if (!array_key_exists($field, $searches)) {
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
        $operator = 'eq';

        if (isset($type[0])) {
            $operator = $type[0];
        }

        if (isset($type['map'])) {
            $field = $type['map'];
        }

        if ($operator === 'eq') {
            $builder->where($field, $value);
            return;
        }

        if ($operator === 'like') {
            $builder->where($field, 'like', "%$value%");
            return;
        }

        if ($operator === 'gt') {
            $builder->where($field, '>', $value);
            return;
        }

        if ($operator === 'gte') {
            $builder->where($field, '>=', $value);
            return;
        }

        if ($operator === 'lt') {
            $builder->where($field, '<', $value);
            return;
        }

        if ($operator === 'lte') {
            $builder->where($field, '<=', $value);
            return;
        }
    }
}
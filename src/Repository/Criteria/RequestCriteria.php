<?php

namespace Hypocenter\LaravelVitamin\Repository\Criteria;


class RequestCriteria extends AbstractCriteria
{
    protected $field = 's';

    public function create($searchable = null, $field = null)
    {
        $c = new static();

        if ($searchable) {
            $c->setSearchable($searchable);
        }

        if ($field) {
            $c->setField($field);
        }

        return $c;
    }

    public function setField($field)
    {
        $this->field = $field;
        return $this;
    }

    protected function receive()
    {
        return request($this->field);
    }
}
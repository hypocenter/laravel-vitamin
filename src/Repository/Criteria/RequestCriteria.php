<?php

namespace Hypocenter\LaravelVitamin\Repository\Criteria;


class RequestCriteria extends AbstractCriteria
{
    protected $field = 's';

    /**
     * @param null $searchable
     * @param null $field
     *
     * @return RequestCriteria|static
     *
     * @deprecated use set() instead, will remove in v0.2.0
     */
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

    public function set($searchable = null, $field = null)
    {
        if ($searchable) {
            $this->setSearchable($searchable);
        }

        if ($field) {
            $this->setField($field);
        }

        return $this;
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
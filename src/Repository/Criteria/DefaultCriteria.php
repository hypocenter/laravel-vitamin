<?php

namespace Hypocenter\LaravelVitamin\Repository\Criteria;


class DefaultCriteria extends AbstractCriteria
{
    protected $searches;

    public function set($searchable, $searches)
    {
        $this->setSearchable($searchable);
        $this->searches = $searches;
        return $this;
    }

    protected function receive()
    {
        return $this->searches;
    }
}
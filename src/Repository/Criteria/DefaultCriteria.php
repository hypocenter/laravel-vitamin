<?php

namespace Hypocenter\LaravelVitamin\Repository\Criteria;


class DefaultCriteria extends AbstractCriteria
{
    protected $searches;

    public function set($searches)
    {
        $this->searches = $searches;
        return $this;
    }

    protected function receive()
    {
        return $this->searches;
    }
}
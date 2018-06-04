<?php

namespace Hypocenter\LaravelVitamin\Repository\Criteria;


use Hypocenter\LaravelVitamin\Repository\Contracts\CriteriaParser;

class RequestCriteria extends AbstractCriteria
{
    protected $field = 's';
    /**
     * @var CriteriaParser
     */
    private $parser;

    public function __construct(CriteriaParser $parser)
    {
        $this->parser = $parser;
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

    public function get()
    {
        return $this->receive();
    }

    protected function receive()
    {
        return $this->parser->parse(request($this->field));
    }
}
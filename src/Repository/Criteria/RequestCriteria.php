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
        $c = resolve(get_called_class());

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
        return $this->parser->parse(request($this->field));
    }
}
<?php

namespace Hypocenter\LaravelVitamin\Repository\Eloquent\Criteria;


use Hypocenter\LaravelVitamin\Repository\Contracts\CriteriaParser;
use Hypocenter\LaravelVitamin\Repository\Eloquent\EloquentCriteria;

class RequestCriteria extends EloquentCriteria
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

    public function apply($builder)
    {
        $this->searches = $this->parser->parse(request($this->field));
        parent::apply($builder);
    }

    public function setField($field)
    {
        $this->field = $field;
        return $this;
    }
}
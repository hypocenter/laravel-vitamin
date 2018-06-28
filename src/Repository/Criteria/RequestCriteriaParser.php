<?php

namespace Hypocenter\LaravelVitamin\Repository\Criteria;


use Hypocenter\LaravelVitamin\Repository\Contracts\CriteriaParser;

class RequestCriteriaParser implements CriteriaParser
{
    /**
     * @var CriteriaParser
     */
    private $parser;

    public function __construct(CriteriaParser $parser)
    {
        $this->parser = $parser;
    }

    public function parse($field = 's')
    {
        return $this->parser->parse(request($field));
    }
}
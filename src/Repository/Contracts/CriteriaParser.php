<?php

namespace Hypocenter\LaravelVitamin\Repository\Contracts;


interface CriteriaParser
{
    public function parse($value);
}
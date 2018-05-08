<?php

namespace Hypocenter\LaravelVitamin\Repository\Contracts;


interface Criteria
{
    public function apply($builder);
}
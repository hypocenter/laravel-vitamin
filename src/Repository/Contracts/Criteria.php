<?php

namespace Hypocenter\LaravelVitamin\Repository\Contracts;


interface Criteria
{
    const OP_EQ   = 'eq';
    const OP_LIKE = 'like';
    const OP_GT   = 'gt';
    const OP_GTE  = 'gte';
    const OP_LT   = 'lt';
    const OP_LTE  = 'lte';
    const OP_IN   = 'in';
    CONST OP_CST  = 'custom';

    public function apply($builder);
}
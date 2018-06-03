<?php

namespace Hypocenter\LaravelVitamin\Repository\Contracts;


interface Criteria
{
    const OP_EQ         = 'eq';
    const OP_LIKE       = 'like';
    const OP_GT         = 'gt';
    const OP_GTE        = 'gte';
    const OP_LT         = 'lt';
    const OP_LTE        = 'lte';
    const OP_IN         = 'in';
    const OP_START_WITH = 'start_with';
    const OP_END_WITH   = 'end_with';
    const OP_CUSTOM     = 'custom';

    public function apply($builder);
}
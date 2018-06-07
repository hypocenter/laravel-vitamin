<?php

namespace Hypocenter\LaravelVitamin\Repository\Criteria;


use Hypocenter\LaravelVitamin\Repository\Contracts\CriteriaParser;

/**
 * Class DefaultCriteriaParser
 *
 * usage: p1:name;p2:1,2,3;p3:30
 *
 * @package Hypocenter\LaravelVitamin\Repository\Criteria
 */
class DefaultCriteriaParser implements CriteriaParser
{
    public function parse($value, $mappings = [])
    {
        $searches = [];

        if (empty($value)) {
            return $searches;
        }

        foreach (explode(';', $value) as $v) {
            $v = explode(':', $v, 2);
            if (count($v) !== 2 || empty($v[0]) || $v[1] === '') {
                continue;
            }

            $key = isset($mpppings[$v[0]]) ? $mappings[$v[0]] : $v[0];

            if (strpos($v[1], ',') !== false) {
                $v[1] = explode(',', $v[1]);
            }

            $searches[$key] = $v[1];
        }

        return $searches;
    }
}
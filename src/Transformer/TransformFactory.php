<?php

namespace Hypocenter\LaravelVitamin\Transformer;


class TransformFactory
{
    public function api($data)
    {
        return ApiTransformer::create($data);
    }

    public function array($data)
    {
        return ArrayTransformer::create($data);
    }

    public function composite($data, $transforms = [])
    {
        return CompositeTransformer::create($data, $transforms);
    }

    public function equivalent($data)
    {
        return EquivalentTransformer::create($data);
    }

    public function paginator($data)
    {
        return PaginatorTransformer::create($data);
    }

    public function rules($data, $rules = [])
    {
        return RuleTransformer::create($data, $rules);
    }

    public function paginatedRules($data, $rules = [])
    {
        return CompositeTransformer::create($data, [
            PaginatorTransformer::class,
            'data' => function ($data) use ($rules) {
                return RuleTransformer::create($data, $rules);
            },
        ]);
    }
}
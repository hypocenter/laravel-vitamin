<?php

namespace Hypocenter\LaravelVitamin\Transformer;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ModelTransformer extends AbstractTransformer
{
    public function transform()
    {
        /** @var Model $model */
        $model = $this->getData();
        $data = $model->attributesToArray();

        foreach ($model->getRelations() as $k => $v) {
            $cls = get_class($model);
            if ($cls::$snakeAttributes) {
                $k = Str::snake($k);
            }

            $data[$k] = $v;
        }

        return ArrayTransformer::create($data)->transform();
    }

}
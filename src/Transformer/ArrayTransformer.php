<?php

namespace Hypocenter\LaravelVitamin\Transformer;


use Illuminate\Contracts\Support\Arrayable;

class ArrayTransformer extends AbstractTransformer
{
    public function transform()
    {
        return $this->handle($this->get());
    }

    protected function handle($value)
    {
        if (is_iterable($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = static::create($v)->transform();
                if ($value[$k] instanceof Arrayable) {
                    $value[$k] = $value[$k]->toArray();
                }
            }
        }

        if ($value instanceof Arrayable) {
            $value = $value->toArray();
        }

        return $value;
    }
}
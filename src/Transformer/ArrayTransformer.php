<?php

namespace Hypocenter\LaravelVitamin\Transformer;


use Illuminate\Contracts\Support\Arrayable;

class ArrayTransformer extends AbstractTransformer
{
    public function transform()
    {
        return (array)$this->handle($this->handleTransformer($this->getData()));
    }

    protected function handle($data)
    {
        if (is_iterable($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = $this->handle($this->handleTransformer($v));
            }
        }

        if ($data instanceof Arrayable) {
            $data = $this->handle($data->toArray());
        }

        return $data;
    }
}
<?php

namespace Hypocenter\LaravelVitamin\Transformer;


class DefaultTransformer extends AbstractTransformer
{
    public function transform()
    {
        return $this->get();
    }
}
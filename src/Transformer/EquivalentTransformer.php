<?php

namespace Hypocenter\LaravelVitamin\Transformer;


class EquivalentTransformer extends AbstractTransformer
{
    public function transform()
    {
        return $this->get();
    }
}
<?php

namespace Hypocenter\LaravelVitamin\Transformer\Contracts;


interface Transformable
{
    public function getTransformer(): string;
}
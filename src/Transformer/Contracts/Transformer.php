<?php

namespace Hypocenter\LaravelVitamin\Transformer\Contracts;


interface Transformer
{
    public static function create($data);

    public function transform();
}
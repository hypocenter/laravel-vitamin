<?php

namespace Hypocenter\LaravelVitamin\Error;


use Hypocenter\LaravelVitamin\Support\Enum;

abstract class Error extends Enum
{
    public function msg()
    {
        return $this->name();
    }

    public function status()
    {
        return $this->value();
    }

    public function createException()
    {
        return new ErrorException($this);
    }
}
<?php

namespace Hypocenter\LaravelVitamin\Validator\Contracts;


interface Validatable
{
    public function getValidator(): string;
}
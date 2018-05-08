<?php

namespace Hypocenter\LaravelVitamin\Container\Contracts;


use Illuminate\Contracts\Container\Container;

interface ContainerAwareInterface
{
    public function setContainer(Container $container);
}
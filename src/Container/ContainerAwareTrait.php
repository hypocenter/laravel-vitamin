<?php

namespace Hypocenter\LaravelVitamin\Container;


use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Application;

trait ContainerAwareTrait
{
    /**
     * @var Container|Application
     */
    private $container;

    public function setContainer(Container $container)
    {
        $this->container = $container;
    }
}
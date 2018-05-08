<?php

namespace Hypocenter\LaravelVitamin\Providers;


use Hypocenter\LaravelVitamin\Container\Contracts\BootableInterface;
use Hypocenter\LaravelVitamin\Container\Contracts\ContainerAwareInterface;
use Hypocenter\LaravelVitamin\Repository\Contracts\CriteriaParser;
use Hypocenter\LaravelVitamin\Repository\Criteria\DefaultCriteriaParser;
use Illuminate\Support\ServiceProvider;

class LaravelVitaminServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(CriteriaParser::class, DefaultCriteriaParser::class);

        $this->app->resolving(function ($object, $app) {
            if ($object instanceof ContainerAwareInterface) {
                $object->setContainer($app);
            }

            if ($object instanceof BootableInterface) {
                $object->boot();
            }
        });
    }
}
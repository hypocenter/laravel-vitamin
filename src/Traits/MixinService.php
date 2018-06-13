<?php

namespace Hypocenter\LaravelVitamin\Traits;


trait MixinService
{
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->service, $name], $arguments);
    }
}

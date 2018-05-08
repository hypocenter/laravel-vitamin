<?php

namespace Hypocenter\LaravelVitamin\Validator;


use Hypocenter\LaravelVitamin\Validator\Contracts\Validator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

abstract class AbstractValidator implements Validator
{
    public function rules($scene = null): array
    {
        return $this->callSceneMethod('rules', $scene);
    }

    public function messages($scene = null): array
    {
        return $this->callSceneMethod('messages', $scene);
    }

    protected function rulesOnSave(): array
    {
        return [];
    }

    /**
     * @param      $modelOrAttributes
     * @param null $scene
     *
     * @return array
     * @throws ValidationException
     */
    public function validate($modelOrAttributes, $scene = null)
    {
        if ($modelOrAttributes instanceof Model) {
            $data = [];
            foreach (array_keys($modelOrAttributes->getAttributes()) as $k) {
                $data[$k] = $modelOrAttributes->getAttributeValue($k);
            }
            $modelOrAttributes = $data;
        } else if ($modelOrAttributes instanceof Arrayable) {
            $modelOrAttributes = $modelOrAttributes->toArray();
        }

        $validator = \Validator::make($modelOrAttributes, $this->rules($scene), $this->messages($scene));

        return $validator->validate();
    }

    /**
     *
     * @param      $type
     * @param null $scene
     *
     * @return null
     */
    private function callSceneMethod($type, $scene = null)
    {
        $methods = ['save'];

        if ($scene) {
            array_unshift($methods, $scene);
        }

        foreach ($methods as $method) {
            $method = $type . "On" . ucfirst(Str::camel($method));
            if (method_exists($this, $method)) {
                return $this->$method();
            }
        }

        return [];
    }
}
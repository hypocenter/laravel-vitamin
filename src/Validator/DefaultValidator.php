<?php

namespace Hypocenter\LaravelVitamin\Validator;


use Hypocenter\LaravelVitamin\Validator\Contracts\Validator;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class DefaultValidator extends AbstractValidator
{
    private $rules    = [];
    private $messages = [];
    private $scene    = null;

    public function __construct($scene = null)
    {
        $this->scene = $scene;
    }

    /**
     * @param array      $rules
     * @param array|null $messages
     *
     * @return $this|static
     */
    public function add(array $rules, array $messages = null)
    {
        foreach ($rules as $field => $rule) {
            if (Str::endsWith($field, '.')) {
                if (is_array($rule)) {
                    if (isset($rule[0])) {
                        $this->addValidator($field, $rule[0], array_only($rule, ['only', 'except', 'scene']));
                    } else {
                        $this->addChildRules($field, $rule);
                    }
                } else {
                    $this->addValidator($field, $rule);
                }
            } else {
                $this->rules[$field] = $rule;
            }
        }

        $this->messages = array_merge($this->messages, $messages ?: []);

        return $this;
    }

    public function rules($scene = null): array
    {
        return $this->rules;
    }

    public function messages($scene = null): array
    {
        return $this->messages;
    }

    private function addValidator($field, $validator, $configs = [])
    {
        $validator = $validator instanceof Validator ? $validator : resolve($validator);
        assert($validator instanceof Validator);

        $only = $configs['only'] ?? [];
        $only = is_string($only) ? explode(',', $only) : Arr::wrap($only);

        $excepts = $configs['except'] ?? [];
        $excepts = is_string($excepts) ? explode(',', $excepts) : Arr::wrap($excepts);

        $scene = isset($configs['scene']) ? $configs['scene'] : $this->scene;

        $this->addChildRules($field, $validator->rules($scene), $only, $excepts);

        foreach ($validator->messages($scene) as $k => $m) {
            if (in_array($k, $excepts) || !in_array($k, $only)) {
                continue;
            }

            $this->messages["{$field}{$k}"] = $m;
        }
    }

    private function addChildRules($field, $rules, $only = null, $excepts = null)
    {
        foreach ($rules as $k => $r) {
            if (($excepts && in_array($k, $excepts)) || ($only && !in_array($k, $only))) {
                continue;
            }

            $this->rules["{$field}{$k}"] = $r;
        }
    }

}
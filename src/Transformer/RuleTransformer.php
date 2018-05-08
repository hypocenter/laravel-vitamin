<?php

namespace Hypocenter\LaravelVitamin\Transformer;


use Hypocenter\LaravelVitamin\Transformer\Contracts\Transformable;
use Hypocenter\LaravelVitamin\Transformer\Contracts\Transformer;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

/**
 * Class RuleTransformer
 *
 * ##Rules:
 * 函数: intval, trim
 * 标量: int,float,double,string,bool,any
 * 数组: int[], float[], double[], string[]
 * 对象: int{}, float{}, double{}, string{}
 * 数据结构: ["a" => "string", "b" => "int", "c" => ["a" => "string", "b" => "string[]"]]
 * 数据结构数组: [[ "a" => "string", "b" => "int", "c" => [[ "aa" => "int", "bb" => "float" ]] ]]
 * 转换器: App\Transformers\UserTransformer
 *
 * @package Hypocenter\LaravelVitamin\Transformer
 */
class RuleTransformer extends AbstractTransformer
{
    private $rules;

    public static function create($data, $rules = [])
    {
        $tr = parent::create($data);
        $tr->setRules($rules);
        return $tr;
    }

    public function setRules($rules)
    {
        return $this->rules = $rules;
    }

    public function transform()
    {
        return $this->convert($this->get(), $this->rules);
    }

    private function convert($data, $rules)
    {
        if ($data instanceof Transformable) {
            $cls  = $data->getTransformer();
            $data = new $cls($data);
        }

        if ($data instanceof Transformer) {
            $data = $data->transform();
        }

        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        }

        if (is_string($rules)) {
            return $this->to($data, $rules);
        }

        if (is_array($rules)) {
            $data = $data ? (array)$data : $data;
            if (isset($rules[0]) && count($rules) === 1) {
                // 定义某个数据结构的数组
                $rule = $rules[0];
                return array_map(function ($row) use ($rule) {
                    return $this->convert($row, $rule);
                }, $data ?: []);
            } else if (isset($rules['map'])) {
                // 定义Map
                $rule = $rules['map'];
                if (empty($data)) {
                    return null;
                }
                return array_map(function ($value) use ($rule) {
                    return $this->convert($value, $rule);
                }, $data);
            } else {
                // 定义某个数据结构
                if (empty($data)) {
                    return null;
                }
                $res = [];
                foreach ($rules as $k => $rule) {
                    if (array_key_exists($k, $data)) {
                        $res[$k] = $this->convert($data[$k], $rule);
                    }
                }
                return $res;
            }
        }

        throw new \InvalidArgumentException("\$rules type must be string or array");
    }

    private function to($value, $type)
    {
        if (Str::endsWith($type, '[]')) {
            $type = substr($type, 0, -2);
            return $value ? array_values(array_map(function ($v) use ($type) {
                return $this->to($v, $type);
            }, $value)) : null;
        }

        if (Str::endsWith($type, '{}')) {
            $type = substr($type, 0, -2);
            if (!$value) {
                return null;
            }
            foreach ($value as $k => $v) {
                $value[$k] = $this->to($v, $type);
            }

            return $value;
        }

        switch (true) {
            case $type === 'string':
                return strval($value);
            case $type === 'int':
                return intval($this->ensureNumber($value));
            case $type === 'float' || $type === 'double':
                return doubleval($this->ensureNumber($value));
            case $type === 'array':
                return (array)$value;
            case $type === 'bool':
                return !!$value;
            case $type === 'any':
                return $value;
            case Str::endsWith($type, 'Transformer'):
                $cls = '\\' . trim($type, '\\');
                return $cls::create($value)->transform();
            case is_callable($type):
                return $type($value);
            default:
                throw new \InvalidArgumentException("Unsupported type: $type");
        }
    }

    private function ensureNumber($value)
    {
        if (is_null($value)) {
            return 0;
        }

        if (is_object($value)) {
            $value = strval($value);
        }

        if (!is_numeric($value)) {
            throw new \InvalidArgumentException('Invalid number value: ' . $value);
        }

        return $value;
    }
}
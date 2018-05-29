<?php

namespace Hypocenter\LaravelVitamin\Transformer;


use Hypocenter\LaravelVitamin\Transformer\Contracts\Transformable;
use Hypocenter\LaravelVitamin\Transformer\Contracts\Transformer;

const TYPE_OBJECT = 'object';
const TYPE_ARRAY  = 'array';
const TYPE_SCALAR = 'scalar';

abstract class AbstractTransformer implements Transformer, \JsonSerializable
{
    private $data;
    private $type;

    /**
     * @param $data
     *
     * @return static
     */
    public static function create($data)
    {
        $tr = resolve(get_called_class());
        $tr->set($data);
        return $tr;
    }

    public function set($data)
    {
        $this->data = $data;

        if (is_array($data)) {
            $this->type = TYPE_ARRAY;
        } else if (is_object($data)) {
            $this->type = TYPE_OBJECT;
        } else {
            $this->type = TYPE_SCALAR;
        }
    }

    protected function getData()
    {
        return $this->data;
    }

    protected function getType()
    {
        return $this->type;
    }

    protected function get(string $key = null)
    {
        $value = null;

        if (is_null($key)) {
            $value = $this->data;
        } else {
            switch ($this->type) {
                case TYPE_OBJECT:
                    $value = $this->data->$key ?? null;
                    break;
                case TYPE_ARRAY:
                    $value = $this->data[$key] ?? null;
                    break;
                default:
                    throw new \InvalidArgumentException('Scalar type unsupported get by key.');
            }
        }

        return $this->handleTransformer($value);
    }

    protected function handleTransformer($value)
    {
        if ($value instanceof Transformable) {
            $cls = $value->getTransformer();
            if ($cls !== get_called_class()) {
                /** @noinspection PhpUndefinedMethodInspection */
                $value = $cls::create($value);
            }
        }

        if ($value instanceof Transformer) {
            $value = $value->transform();
        }

        return $value;
    }

    public function required(string $key = null)
    {
        $value = $this->get($key);
        if (is_null($value)) {
            throw new \OutOfBoundsException('Transformer ' . get_class($this) . ' require ' . $key);
        }

        return $value;
    }

    public function jsonSerialize()
    {
        return $this->transform();
    }

    public function __toString()
    {
        return json_encode($this->transform());
    }
}
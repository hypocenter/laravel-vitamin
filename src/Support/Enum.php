<?php

namespace Hypocenter\LaravelVitamin\Support;


use Illuminate\Contracts\Support\Arrayable;

abstract class Enum implements \JsonSerializable, Arrayable
{
    const __default = null;

    private $value;

    private $key;

    private $strict;

    protected static $valueType;

    protected static $nameMaps = [];

    protected static $metaMaps = [];

    /**
     * 是否为 SoftMode
     * 如果为 true, 初始化一个未定义的值, 会依据 $softModeWarn 触发一个警告或者静默, 用于值可能不收控制改变的情况
     * 如果为 false, 则直接报异常, 用于值非常确定的情况
     *
     * @default true
     *
     * @var bool
     */
    protected static $softMode = false;

    /**
     * 在 SoftMode 为 true 的情况下, 是否触发警告
     *
     * 设置为 true, 当外部值发生变化, 可以及时通过日志知晓, 以更新代码
     * 然而, 对于外部变化并不关心的情况, 可以设置为 false
     *
     * @default true
     *
     * @var bool
     */
    protected static $softModeWarn = true;

    private static $constants = [];

    public static function getConstants($includeDefault = false)
    {
        $class = get_called_class();

        if (!isset(self::$constants[$class])) {
            self::populateConstants();
        }

        return $includeDefault ? array_merge(self::$constants[$class], [
            "__default" => self::__default,
        ]) : self::$constants[$class];
    }

    /**
     * @param      $value
     * @param bool $strict
     *
     * @return static
     */
    public static function fromValue($value, $strict = true)
    {
        if ($value instanceof static) {
            return clone $value;
        }

        $class = get_called_class();
        return new $class($value, $strict);
    }

    /**
     * @param      $key
     * @param bool $strict
     *
     * @return static
     */
    public static function fromKey($key, $strict = true)
    {
        $class     = get_called_class();
        $constants = self::getConstants();
        if (!isset($constants[$key])) {
            throw new \InvalidArgumentException(get_called_class() . 'Key "' . $key . '" is not in enum ' . $class);
        }

        return new $class($constants[$key], $strict);
    }

    public function __construct($initialValue = null, $strict = true)
    {
        $initialValue = $this->convertValue($initialValue);

        $class = get_called_class();

        if (!array_key_exists($class, self::$constants)) {
            self::populateConstants();
        }

        if ($initialValue === null) {
            $initialValue = self::$constants[$class]["__default"];
        }

        $temp = self::$constants[$class];

        $key = '';
        if (!in_array($initialValue, $temp, $strict)) {
            if (static::$softMode) {
                $key = 'KEY' . $initialValue;
                static::$softModeWarn && trigger_error(get_called_class() . ': Value "' . $initialValue . '" is not in soft mode enum', E_USER_WARNING);
            } else {
                throw new \InvalidArgumentException(get_called_class() . 'Value "' . $initialValue . '" is not in enum ' . $class);
            }

        }

        $this->value  = $initialValue;
        $this->strict = $strict;

        $key       = $key ?: array_search($initialValue, $temp, $strict);
        $this->key = $key;
    }

    private function populateConstants()
    {
        $class                   = get_called_class();
        $constants               = (new \ReflectionClass($class))->getConstants();
        self::$constants[$class] = $constants;
    }

    protected function convertValue($value)
    {
        switch (static::$valueType) {
            case 'int':
                return intval($value);
            case 'float':
                return floatval($value);
            case 'string':
                return strval($value);
            default:
                return $value;
        }
    }

    public function __toString()
    {
        return (string)$this->value;
    }

    public function equals($object)
    {
        if (is_object($object)) {
            if (!($object instanceof Enum)) {
                return false;
            }

            $value = $object->value();
        } else {
            $value = $object;
        }

        return $this->strict ? ($this->value === $value)
            : ($this->value == $value);

    }

    public function key()
    {
        return $this->key;
    }

    public function value()
    {
        return $this->value;
    }

    public function name()
    {
        if (isset(static::$nameMaps[$this->value()])) {
            return static::$nameMaps[$this->value()];
        } else {
            return $this->key();
        }
    }

    public function meta()
    {
        if (isset(static::$metaMaps[$this->value()])) {
            return static::$metaMaps[$this->value()];
        } else {
            return null;
        }
    }

    public function toArray()
    {
        return ['key' => $this->key(), 'value' => $this->value(), 'name' => $this->name(), 'meta' => $this->meta()];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
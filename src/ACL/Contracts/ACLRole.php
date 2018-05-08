<?php

namespace Hypocenter\LaravelVitamin\ACL\Contracts;


use Illuminate\Contracts\Support\Arrayable;

interface ACLRole extends \JsonSerializable, Arrayable
{
    /**
     * @return mixed
     */
    public function getId();

    /**
     * @param mixed $id
     */
    public function setId($id): void;

    /**
     * @return mixed
     */
    public function getName();

    /**
     * @param mixed $name
     */
    public function setName($name): void;

    /**
     * @return array
     */
    public function getRules(): array;

    /**
     * @param array $rules
     */
    public function setRules(array $rules): void;
}
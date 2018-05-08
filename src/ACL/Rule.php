<?php

namespace Hypocenter\LaravelVitamin\ACL;


use Hypocenter\LaravelVitamin\ACL\Contracts\ACLRule;

class Rule implements ACLRule
{
    private $id;
    private $name;

    public function __construct($id, $name)
    {
        $this->id   = $id;
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    public function toArray()
    {
        return [
            'id'   => $this->id,
            'name' => $this->name,
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function __get($name)
    {
        switch ($name) {
            case 'id':
                return $this->getId();
            case 'name';
                return $this->getName();
        }

        throw new \InvalidArgumentException('No field named ' . $name);
    }
}
<?php

namespace Hypocenter\LaravelVitamin\ACL;


use Hypocenter\LaravelVitamin\ACL\Contracts\ACLRole;

class Role implements ACLRole
{
    private $id;
    private $name;
    /**
     * @var array
     */
    private $rules;

    public function __construct($id, $name, $rules)
    {
        $this->id    = $id;
        $this->name  = $name;
        $this->rules = $rules ?: [];
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

    /**
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @param array $rules
     */
    public function setRules(array $rules): void
    {
        $this->rules = $rules;
    }

    public function toArray()
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'rules' => $this->rules,
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
            case 'rules':
                return $this->getRules();
        }

        throw new \InvalidArgumentException('No field named ' . $name);
    }
}
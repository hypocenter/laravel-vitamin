<?php

namespace Hypocenter\LaravelVitamin\ACL\Contracts;


interface ACLRule
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
}
<?php

namespace Hypocenter\LaravelVitamin\ACL\Contracts;


interface Authorizable
{
    /**
     * @return string[]|ACLRole[]
     */
    public function getUserACLRoles(): array;
}
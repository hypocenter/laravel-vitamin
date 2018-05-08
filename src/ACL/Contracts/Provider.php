<?php

namespace Hypocenter\LaravelVitamin\ACL\Contracts;


interface Provider
{
    /**
     * @return ACLRule[]
     */
    public function getRules(): array;

    /**
     * @return ACLRole[]
     */
    public function getRoles(): array;

    public function getRule($id): ACLRule;

    public function getRole($id): ACLRole;

    /**
     * @param Authorizable $user
     *
     * @return ACLRule[]
     */
    public function getUserRules(Authorizable $user): array;

    /**
     * @param Authorizable $user
     * @param              $rules
     *
     * @return bool
     */
    public function can(Authorizable $user, $rules): bool;

    /**
     * @param Authorizable $user
     * @param              $roles
     *
     * @return bool
     */
    public function is(Authorizable $user, $roles): bool;
}
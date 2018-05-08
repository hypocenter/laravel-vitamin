<?php

namespace Hypocenter\LaravelVitamin\ACL;


use Hypocenter\LaravelVitamin\ACL\Contracts\ACLRole;
use Hypocenter\LaravelVitamin\ACL\Contracts\ACLRule;
use Hypocenter\LaravelVitamin\ACL\Contracts\Authorizable;
use Hypocenter\LaravelVitamin\ACL\Contracts\Provider;

class ACL
{
    /**
     * @var Provider
     */
    private $provider;

    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @return ACLRule[]
     */
    public function getRules(): array
    {
        return $this->provider->getRules();
    }

    /**
     * @return ACLRole[]
     */
    public function getRoles(): array
    {
        return $this->provider->getRoles();
    }

    /**
     * @param Authorizable $user
     *
     * @return ACLRule[]
     */
    public function getUserRules(Authorizable $user): array
    {
        return $this->provider->getUserRules($user);
    }

    /**
     * @param Authorizable $user
     *
     * @return ACLRole[]
     */
    public function getUserRoles(Authorizable $user): array
    {
        $roles = [];

        foreach (($user->getUserACLRoles() ?: []) as $role) {
            if ($role instanceof ACLRole) {
                $roles[$role->getId()] = $role;
            } else {
                $r = $this->provider->getRole(strval($role));
                if (!$role) {
                    trigger_error('ACL: invalid user role ' . $role, E_USER_WARNING);
                }

                $roles[$r->getId()] = $r;
            }
        }

        return $roles;
    }

    /**
     * @param Authorizable $user
     * @param              $rules
     *
     * @return bool
     */
    public function can(Authorizable $user, $rules): bool
    {
        return $this->provider->can($user, $rules);
    }

    /**
     * @param Authorizable $user
     * @param              $roles
     *
     * @return bool
     */
    public function is(Authorizable $user, $roles): bool
    {
        return $this->provider->is($user, $roles);
    }
}
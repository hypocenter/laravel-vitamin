<?php

namespace Hypocenter\LaravelVitamin\ACL;


use Hypocenter\LaravelVitamin\ACL\Contracts\ACLRule;
use Hypocenter\LaravelVitamin\ACL\Contracts\Authorizable;

class ACLUser
{
    private $acl;
    private $user;

    public function __construct(ACL $acl, Authorizable $user)
    {
        $this->acl  = $acl;
        $this->user = $user;
    }

    /**
     * @return ACLRule[]
     */
    public function getRules(): array
    {
        return $this->acl->getUserRules($this->user);
    }

    /**
     * @return ACLRule[]
     */
    public function getRoles(): array
    {
        return $this->acl->getUserRoles($this->user);
    }

    /**
     * @param $rules
     *
     * @return bool
     */
    public function can($rules): bool
    {
        return $this->acl->can($this->user, $rules);
    }

    /**
     * @param $roles
     *
     * @return bool
     */
    public function is($roles): bool
    {
        return $this->acl->is($this->user, $roles);
    }
}
<?php

namespace Hypocenter\LaravelVitamin\ACL\Providers;


use Hypocenter\LaravelVitamin\ACL\Contracts\ACLRole;
use Hypocenter\LaravelVitamin\ACL\Contracts\ACLRule;
use Hypocenter\LaravelVitamin\ACL\Contracts\Authorizable;
use Hypocenter\LaravelVitamin\ACL\Contracts\Provider;
use Hypocenter\LaravelVitamin\ACL\Role;
use Hypocenter\LaravelVitamin\ACL\Rule;

class ConfigBasedProvider implements Provider
{
    /**
     * @var Rule[]
     */
    protected $rules = [];

    /**
     * @var Role[]
     */
    protected $roles = [];

    public function setRules(array $rules)
    {
        foreach ($rules as $id => $name) {
            $this->rules[$id] = new Rule($id, $name);
        }
    }

    public function setRoles(array $roles)
    {
        foreach ($roles as $id => $role) {
            $this->roles[$id] = new Role($id, $role[0], array_slice($role, 1));
        }
    }

    public function getRules(): array
    {
        return $this->rules;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getRule($id): ACLRule
    {
        return $this->rules[$id] ?? null;
    }

    public function getRole($id): ACLRole
    {
        return $this->roles[$id] ?? null;
    }

    /**
     * @param Authorizable $user
     *
     * @return ACLRule[]
     */
    public function getUserRules(Authorizable $user): array
    {
        $rules = [];

        foreach ($user->getUserACLRoles() as $role) {
            if (!$role instanceof ACLRole) {
                $role = (string)$role;
                if (!isset($this->roles[$role])) {
                    trigger_error('ACL: invalid user role ' . $role, E_USER_WARNING);
                    continue;
                }
                $role = $this->roles[$role];
            }

            $rules = array_merge($rules, $this->role2rules($role));
        }

        return $rules;
    }

    public function can(Authorizable $user, $rules): bool
    {
        $rules     = (array)$rules;
        $userRules = array_keys($this->getUserRules($user));

        foreach ($rules as $rule) {
            if (in_array($rule, $userRules)) {
                return true;
            }
        }

        return false;
    }

    public function is(Authorizable $user, $roles): bool
    {
        $roles     = (array)$roles;
        $userRoles = array_map(function ($role) {
            return $role instanceof ACLRole ? $role->getId() : $role;
        }, $user->getUserACLRoles());

        foreach ($roles as $role) {
            if (!in_array($role, $userRoles)) {
                return false;
            }
        }

        return false;
    }

    private function role2rules(ACLRole $role)
    {
        $rules = [];

        foreach ($role->getRules() as $rule) {
            $s = strpos($rule, '*');

            if ($s === false) {
                if (isset($this->rules[$rule])) {
                    $rules[] = $this->rules[$rule];
                    trigger_error('ACL: invalid user rule ' . $rule, E_USER_WARNING);
                    continue;
                }
            } else if ($s === 0) {
                return $this->getRules(); // 超级权限
            } else {
                $pre   = substr($rule, 0, $s);
                $rules += array_filter($this->rules, function (Rule $rule) use ($s, $pre) {
                    return substr($rule->getId(), 0, $s) === $pre;
                });
            }
        }

        return $rules;
    }
}
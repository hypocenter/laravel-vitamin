<?php

namespace Hypocenter\LaravelVitamin\ACL;


use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Support\ServiceProvider;

class ACLServiceProvider extends ServiceProvider
{
    public function boot(Gate $gate, ACLManager $manager)
    {
        $gate->before(function ($user, $ability) use ($manager) {
            $acl = $manager->forUser($user);
            if ($acl) {
                return $acl->can($ability);
            }

            return null;
        });
    }
}
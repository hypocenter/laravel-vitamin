<?php

namespace Hypocenter\LaravelVitamin\ACL;


use Hypocenter\LaravelVitamin\ACL\Contracts\Provider;
use Illuminate\Support\Str;

class ACLManager
{
    /**
     * @var ACL[]
     */
    private $instances = [];

    /**
     * @var ACLUser[]
     */
    private $mappers = [];

    /**
     * @param $user
     *
     * @return ACLUser|null
     */
    public function forUser($user)
    {
        $id = spl_object_hash($user);

        if (!isset($this->mappers[$id])) {
            foreach ($this->config() as $name => $acl) {
                if ($user instanceof $acl['model']) {
                    $acl                = new ACLUser($this->get($name), $user);
                    $this->mappers[$id] = $acl;
                }
            }
        }

        return $this->mappers[$id] ?? null;
    }

    /**
     * @param $name
     *
     * @return ACL
     */
    public function get($name)
    {
        if (!isset($this->instances[$name])) {
            $config = $this->config($name);
            if (!$config) {
                throw new \InvalidArgumentException("No ACL config for $name");
            }

            $providerConfig = $config['provider'];

            if (!$providerConfig) {
                throw new \InvalidArgumentException("No ACL provider for $name");
            }

            $provider = $this->resolve($providerConfig);

            $this->instances[$name] = new ACL($provider);
        }

        return $this->instances[$name];
    }

    private function config($name = null)
    {
        return $name ? config("acl.$name") : config('acl');
    }

    private function resolve($config): Provider
    {
        if (is_string($config)) {
            return resolve($config);
        }

        $cls      = $config['driver'];
        $provider = resolve($cls);
        foreach ($config as $k => $v) {
            if ($k === 'driver') {
                continue;
            }

            $method = 'set' . ucfirst(Str::camel($k));
            if (method_exists($provider, $method)) {
                $provider->$method($v);
            }
        }

        return $provider;
    }
}
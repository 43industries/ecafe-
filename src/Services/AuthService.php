<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Contracts\AuthProviderInterface;

class AuthService
{
    /** @var AuthProviderInterface[] */
    private array $providers;

    public function __construct()
    {
        $this->providers = [
            new SSOAuthProvider(),
            new LocalAuthProvider(),
        ];
    }

    public function login(string $identifier, string $password): ?array
    {
        foreach ($this->providers as $provider) {
            $user = $provider->authenticate($identifier, $password);
            if ($user) {
                $user['auth_provider'] = $provider->getProviderName();
                return $user;
            }
        }
        return null;
    }

    public function logout(): void
    {
        \App\Helpers\Session::destroy();
    }
}

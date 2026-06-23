<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface AuthProviderInterface
{
    /**
     * @return array{id:int,role:string,name:string,identifier:string}|null
     */
    public function authenticate(string $identifier, string $secret): ?array;

    public function getProviderName(): string;
}

<?php

declare(strict_types=1);

namespace App\Shared\Application\Gateway;

interface GatewayResponse
{
    /**
     * @return array<string,string>
     */
    public function data(): array;
}

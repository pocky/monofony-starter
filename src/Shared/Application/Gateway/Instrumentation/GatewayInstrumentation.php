<?php

declare(strict_types=1);

namespace App\Shared\Application\Gateway\Instrumentation;

use App\Shared\Application\Gateway\GatewayRequest;
use App\Shared\Application\Gateway\GatewayResponse;

interface GatewayInstrumentation
{
    public function start(GatewayRequest $gatewayRequest): void;

    public function success(GatewayResponse $gatewayResponse): void;

    public function error(GatewayRequest $gatewayRequest, string $reason): void;
}

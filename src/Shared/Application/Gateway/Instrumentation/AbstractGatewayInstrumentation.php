<?php

declare(strict_types=1);

namespace App\Shared\Application\Gateway\Instrumentation;

use App\Shared\Application\Gateway\GatewayRequest;
use App\Shared\Application\Gateway\GatewayResponse;
use App\Shared\Infrastructure\Instrumentation\LoggerInstrumentation;
use Psr\Log\LoggerInterface;

abstract class AbstractGatewayInstrumentation implements GatewayInstrumentation
{
    private LoggerInterface $logger;

    public function __construct(LoggerInstrumentation $loggerInstrumentation)
    {
        $this->logger = $loggerInstrumentation->getLogger();
    }

    public function start(GatewayRequest $gatewayRequest): void
    {
        /** @phpstan-ignore-next-line */
        $this->logger->info(static::NAME, $gatewayRequest->data());
    }

    public function success(GatewayResponse $gatewayResponse): void
    {
        /** @phpstan-ignore-next-line */
        $this->logger->info(\sprintf('%s.success', static::NAME), $gatewayResponse->data());
    }

    public function error(GatewayRequest $gatewayRequest, string $reason): void
    {
        /** @phpstan-ignore-next-line */
        $this->logger->error(\sprintf('%s.error', static::NAME), array_merge(
            $gatewayRequest->data(),
            [' reason' => $reason]
        ));
    }
}

<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Instrumentation;

use Psr\Log\LoggerInterface;

class LoggerInstrumentation implements Instrumentation
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }
}

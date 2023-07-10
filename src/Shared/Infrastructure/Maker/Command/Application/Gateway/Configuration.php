<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Maker\Command\Application\Gateway;

use App\Shared\Infrastructure\Maker\Configuration\NameInterface;
use App\Shared\Infrastructure\Maker\Configuration\PackageInterface;
use App\Shared\Infrastructure\Maker\Enum\Operation;

final readonly class Configuration implements PackageInterface, NameInterface
{
    public function __construct(
        private string $package,
        private string $name,
        private string $entry,
        private Operation $operation,
    ) {
    }

    public function getPackage(): string
    {
        return $this->package;
    }

    public function getPackagePath(): string
    {
        return str_replace('\\', '/', $this->package);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEntry(): string
    {
        return $this->entry;
    }

    public function getOperation(): Operation
    {
        return $this->operation;
    }

    public function getGatewayPrefix(): string
    {
        return sprintf(
            '%s\\Application\\Gateway\\%s\\',
            $this->getPackage(),
            $this->getName(),
        );
    }

    public function getMiddlewarePrefix(): string
    {
        return sprintf(
            '%s\\Application\\Gateway\\%s\\Middleware\\',
            $this->getPackage(),
            $this->getName(),
        );
    }

    public function getRequestTemplate(): string
    {
        return 'application/gateway/Request';
    }

    public function getResponseTemplate(): string
    {
        return 'application/gateway/Response';
    }

    public function getInstrumentationTemplate(): string
    {
        return 'application/gateway/Instrumentation';
    }

    public function getGatewayTemplate(): string
    {
        return 'application/gateway/Gateway';
    }

    public function getErrorTemplate(): string
    {
        return 'application/gateway/Middleware/ErrorHandler';
    }

    public function getLoggerTemplate(): string
    {
        return 'application/gateway/Middleware/Logger';
    }

    public function getProcessorTemplate(): string
    {
        return sprintf('application/gateway/Middleware/Processor/%s', $this->getOperation()->entryClass());
    }
}

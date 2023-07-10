<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Maker\Command\Application\Operation;

use App\Shared\Infrastructure\Maker\Configuration\NameInterface;
use App\Shared\Infrastructure\Maker\Configuration\PackageInterface;
use App\Shared\Infrastructure\Maker\Enum\Operation;

final readonly class Configuration implements PackageInterface, NameInterface
{
    public function __construct(
        private string $package,
        private string $name,
        private Operation $operation,
        private string $domain,
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

    public function getOperation(): Operation
    {
        return $this->operation;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function getPersistenceName(): string
    {
        return $this->getOperation()->type();
    }

    public function getFactoryPrefix(): string
    {
        $pos = strpos($this->getDomain(), $this->getOperation()->entryClass());
        $domain = substr($this->getDomain(), 0, $pos - 1);

        return sprintf(
            '%s\\Data%s\\Factory\\',
            $domain,
            $this->getPersistenceName(),
        );
    }

    public function getOperationPrefix(): string
    {
        return sprintf(
            '%s\\Application\\Operation\\%s\\%s\\',
            $this->getPackage(),
            $this->operation->operationType(),
            $this->getName(),
        );
    }

    public function getOperationHandlerTemplate(): string
    {
        return sprintf('application/operation/Handler/%s', $this->getOperation()->entryClass());
    }

    public function getOperationDataTemplate(): string
    {
        return 'application/operation/Data';
    }

    public function getOperationEventTemplate(): string
    {
        return 'application/operation/Event';
    }
}

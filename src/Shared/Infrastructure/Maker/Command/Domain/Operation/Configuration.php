<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Maker\Command\Domain\Operation;

use App\Shared\Infrastructure\Maker\Configuration\NameInterface;
use App\Shared\Infrastructure\Maker\Configuration\PackageInterface;
use App\Shared\Infrastructure\Maker\Enum\Operation;

final class Configuration implements PackageInterface, NameInterface
{
    public function __construct(
        private readonly string $package,
        private readonly string $name,
        private readonly Operation $operation,
        private readonly string $model,
        private readonly string $identifier,
    ) {
    }

    public function getPackage(): string
    {
        return $this->package;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOperation(): Operation
    {
        return $this->operation;
    }

    public function getModelName(): string
    {
        return $this->model;
    }

    public function getIdentifierName(): string
    {
        return $this->identifier;
    }

    public function getExceptionName(): string
    {
        return sprintf('%s%s', $this->operation->exception(), $this->getModelName());
    }

    public function getFactoryName(): string
    {
        return 'Builder';
    }

    public function getPersistenceName(): string
    {
        return $this->getOperation()->type();
    }

    public function getEntryName(): string
    {
        return $this->getOperation()->entryClass();
    }

    public function getExceptionPrefix(): string
    {
        return sprintf(
            '%s\\Domain\\%s\\Data%s\\Exception\\',
            $this->getPackage(),
            $this->getName(),
            $this->getPersistenceName(),
        );
    }

    public function getModelPrefix(): string
    {
        return sprintf(
            '%s\\Domain\\%s\\Data%s\\Model',
            $this->getPackage(),
            $this->getName(),
            $this->getPersistenceName(),
        );
    }

    public function getFactoryPrefix(): string
    {
        return sprintf(
            '%s\\Domain\\%s\\Data%s\\Factory\\',
            $this->getPackage(),
            $this->getName(),
            $this->getPersistenceName(),
        );
    }

    public function getPersistencePrefix(): string
    {
        return sprintf(
            '%s\\Domain\\%s\\Data%s',
            $this->getPackage(),
            $this->getName(),
            $this->getPersistenceName(),
        );
    }

    public function getEntryPrefix(): string
    {
        return sprintf(
            '%s\\Domain\\%s',
            $this->getPackage(),
            $this->getName(),
        );
    }

    public function getExceptionTemplate(): string
    {
        return 'domain/Layer/Exception/OperationException';
    }

    public function getModelTemplate(): string
    {
        return 'domain/Layer/Model/DomainModel';
    }

    public function getModelListTemplate(): string
    {
        return 'domain/Layer/Model/DomainModelList';
    }

    public function getFactoryTemplate(): string
    {
        return 'domain/Layer/Factory/Builder';
    }

    public function getPersistenceTemplate(): string
    {
        return sprintf('domain/Layer/Persistence/%s', ucfirst($this->getOperation()->entryMethod()));
    }

    public function getEntryTemplate(): string
    {
        return sprintf('domain/Layer/Entry/%s', $this->getOperation()->entryClass());
    }
}

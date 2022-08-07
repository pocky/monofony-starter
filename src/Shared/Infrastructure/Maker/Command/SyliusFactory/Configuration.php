<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Maker\Command\SyliusFactory;

use App\Shared\Infrastructure\Maker\Configuration\NameInterface;
use App\Shared\Infrastructure\Maker\Configuration\PackageInterface;

final class Configuration implements NameInterface, PackageInterface
{
    public function __construct(
        private readonly string $name,
        private readonly string $package,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPackage(): string
    {
        return $this->package;
    }

    public function getFactoryPath(): string
    {
        return sprintf('%s\\Shared\\Infrastructure\\Sylius\\Factory\\', $this->package);
    }

    public function getTemplatePath(): string
    {
        return 'infrastructure/sylius/Factory';
    }

    public function getEntityPath(): string
    {
        return sprintf('%s\\Shared\\Infrastructure\\Persistence\\Doctrine\\ORM\\Entity\\', $this->package);
    }

    public function getIdentityPath(): string
    {
        return sprintf('%s\\Shared\\Infrastructure\\Identity\\', $this->package);
    }
}

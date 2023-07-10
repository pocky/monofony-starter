<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Maker\Command\Doctrine\Form;

use App\Shared\Infrastructure\Maker\Configuration\NameInterface;
use App\Shared\Infrastructure\Maker\Configuration\PackageInterface;

final readonly class Configuration implements NameInterface, PackageInterface
{
    public function __construct(
        private string $name,
        private string $package,
        private string $entity,
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

    public function getPackagePath(): string
    {
        return str_replace('\\', '/', $this->package);
    }

    public function getEntity(): string
    {
        return $this->entity;
    }

    public function getFormPath(): string
    {
        return sprintf('UI\\%s\\Form\\Type\\', $this->package);
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

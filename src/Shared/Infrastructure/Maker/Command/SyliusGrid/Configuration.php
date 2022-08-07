<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Maker\Command\SyliusGrid;

use App\Shared\Infrastructure\Maker\Configuration\PackageInterface;

final class Configuration implements PackageInterface
{
    public function __construct(
        private readonly string $package,
        private readonly string $entity,
    ) {
    }

    public function getPackage(): string
    {
        return $this->package;
    }

    public function getEntity(): string
    {
        return $this->entity;
    }

    public function getGridPath(): string
    {
        return sprintf('UI\\%s\\Grid\\', $this->package);
    }

    public function getTemplatePath(): string
    {
        return 'ui/Grid';
    }
}

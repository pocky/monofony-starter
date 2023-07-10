<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Maker\Command\PackageBuilder;

use App\Shared\Infrastructure\Maker\Configuration\PackageInterface;

final readonly class Configuration implements PackageInterface
{
    public function __construct(
        private string $package,
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
}

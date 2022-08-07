<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Maker\Command\PackageBuilder;

use App\Shared\Infrastructure\Maker\Configuration\PackageInterface;

final class Configuration implements PackageInterface
{
    public function __construct(
        private readonly string $package,
    ) {
    }

    public function getPackage(): string
    {
        return $this->package;
    }
}

<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Maker\Command\Domain\Layer;

use App\Shared\Infrastructure\Maker\Configuration\NameInterface;
use App\Shared\Infrastructure\Maker\Configuration\PackageInterface;
use App\Shared\Infrastructure\Maker\Enum\Operation;

final readonly class Configuration implements PackageInterface, NameInterface
{
    public function __construct(
        private string $package,
        private string $name,
        private Operation $type,
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

    public function getType(): Operation
    {
        return $this->type;
    }
}

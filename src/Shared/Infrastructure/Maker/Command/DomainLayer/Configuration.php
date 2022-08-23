<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Maker\Command\DomainLayer;

use App\Shared\Infrastructure\Maker\Configuration\NameInterface;
use App\Shared\Infrastructure\Maker\Configuration\PackageInterface;
use App\Shared\Infrastructure\Maker\Enum\Operation;

final class Configuration implements PackageInterface, NameInterface
{
    public function __construct(
        private readonly string $package,
        private readonly string $name,
        private readonly Operation $type,
    ){
    }

    public function getPackage(): string
    {
        return $this->package;
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

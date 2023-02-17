<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Maker\Configuration;

interface PackageInterface
{
    public function getPackage(): string;
}

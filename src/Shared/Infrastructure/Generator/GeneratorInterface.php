<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Generator;

interface GeneratorInterface
{
    public static function generate(): string;
}

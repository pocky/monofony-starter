<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Clock;

interface ClockInterface
{
    public function now(): \DateTimeImmutable;
}

<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Clock;

use Symfony\Component\Clock\NativeClock;

final readonly class Clock implements ClockInterface
{
    private NativeClock $date;

    public function __construct()
    {
        $this->date = new NativeClock();
    }

    public function now(): \DateTimeImmutable
    {
        return $this->date->now();
    }
}

<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Instrumentation;

interface Instrumentation
{
    /** @phpstan-ignore-next-line */
    public function getLogger();
}

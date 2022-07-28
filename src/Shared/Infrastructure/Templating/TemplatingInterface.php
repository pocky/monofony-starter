<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Templating;

interface TemplatingInterface
{
    /**
     * @param array<array-key, mixed> $parameters
     */
    public function render(string $name, array $parameters = []): string;
}

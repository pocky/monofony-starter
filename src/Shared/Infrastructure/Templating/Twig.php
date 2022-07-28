<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Templating;

use Twig\Environment;

final class Twig implements TemplatingInterface
{
    public function __construct(private readonly Environment $twig)
    {
    }

    /**
     * @param array<array-key, mixed> $parameters
     */
    public function render(string $name, array $parameters = []): string
    {
        return $this->twig->render($name, $parameters);
    }
}

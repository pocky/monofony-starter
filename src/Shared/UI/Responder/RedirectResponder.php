<?php

declare(strict_types=1);

namespace App\Shared\UI\Responder;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

final class RedirectResponder
{
    /**
     * @param array<array-key, mixed> $headers
     */
    public function __invoke(
        string $uri,
        int $status = 302,
        array $headers = [],
    ): Response {
        return new RedirectResponse($uri, $status, $headers);
    }
}

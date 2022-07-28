<?php

declare(strict_types=1);

namespace App\Shared\UI\Responder;

use App\Shared\Infrastructure\Templating\TemplatingInterface;
use Symfony\Component\HttpFoundation\Response;

final class HtmlResponder
{
    public function __construct(
        private readonly TemplatingInterface $templating
    ) {
    }

    /**
     * @param array<array-key, mixed> $parameters
     * @param array<array-key, mixed> $headers
     */
    public function __invoke(
        string $template,
        array $parameters = [],
        int $status = 200,
        array $headers = []
    ): Response {
        $template = $this->templating->render(
            sprintf('%s.html.twig', $template),
            $parameters
        );

        return new Response($template, $status, $headers);
    }
}

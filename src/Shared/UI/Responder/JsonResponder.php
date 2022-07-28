<?php

declare(strict_types=1);

namespace App\Shared\UI\Responder;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class JsonResponder
{
    /**
     * @param mixed[]  $data
     * @param string[] $headers
     */
    public function __invoke(
        array $data,
        int $status = 200,
        array $headers = []
    ): Response {
        return new JsonResponse($data, $status, $headers);
    }
}

<?php

declare(strict_types=1);

namespace App\Shared\UI\Responder;

use App\Shared\Infrastructure\FileManager\FileDownloader;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class DownloadResponder
{
    public function __construct(
        private readonly FileDownloader $fileDownloader,
    ) {
    }

    /**
     * @param array<array-key, mixed> $headers
     */
    public function __invoke(
        string $encodedFilename,
        string $filename,
        int $status = 200,
        array $headers = [],
    ): Response {
        $file = ($this->fileDownloader)($encodedFilename);

        $response = new StreamedResponse(function () use ($file): void {
            // @phpstan-ignore-next-line
            \Safe\stream_copy_to_stream($file->getStream(), \Safe\fopen('php://output', 'wb'));
        });

        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename,
        );

        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', $file->getMimeType());

        return $response;
    }
}

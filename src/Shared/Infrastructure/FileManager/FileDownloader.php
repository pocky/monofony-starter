<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\FileManager;

use League\Flysystem\FilesystemOperator;

final readonly class FileDownloader
{
    public function __construct(
        private FilesystemOperator $filesystemOperator,
    ) {
    }

    public function __invoke(string $encodedFilename): File
    {
        return new File(
            $this->filesystemOperator->readStream($encodedFilename),
            $this->filesystemOperator->mimeType($encodedFilename),
        );
    }
}

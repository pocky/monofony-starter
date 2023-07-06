<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\FileManager;

use League\Flysystem\FilesystemOperator;
use Ramsey\Uuid\Uuid;

final readonly class FileUploader
{
    public function __construct(
        private FilesystemOperator $filesystemOperator,
    ) {
    }

    /**
     * @throws \Safe\Exceptions\FilesystemException
     * @throws \League\Flysystem\FilesystemException
     * @throws \Safe\Exceptions\StringsException
     */
    public function __invoke(string $file, string $filename): string
    {
        $filename = \Safe\sprintf('%s-%s', Uuid::uuid4()->toString(), $filename);

        $stream = \Safe\fopen($file, 'rb');
        $this->filesystemOperator->writeStream($filename, $stream);
        \Safe\fclose($stream);

        return $filename;
    }
}

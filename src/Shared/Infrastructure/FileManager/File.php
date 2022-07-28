<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\FileManager;

final class File
{
    /**
     * @param bool|resource $stream
     */
    public function __construct(private $stream, private readonly string $mimeType)
    {
    }

    /**
     * @return bool|resource
     */
    public function getStream()
    {
        return $this->stream;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }
}

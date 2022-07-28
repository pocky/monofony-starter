<?php

declare(strict_types=1);

namespace App\Shared\Application\Operation;

abstract class AbstractEvent
{
    public function __construct(
        private string $id,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }
}

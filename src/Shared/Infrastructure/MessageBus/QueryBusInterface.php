<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\MessageBus;

use Symfony\Component\Messenger\Envelope;

interface QueryBusInterface
{
    /**
     * @param Envelope|object $query
     *
     * @return mixed
     */
    public function __invoke($query);
}

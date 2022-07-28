<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\MessageBus;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class AsyncEventBus implements EventBusInterface
{
    use HandleTrait;

    public function __construct(
        private readonly MessageBusInterface $eventBus,
    ) {
    }

    /**
     * @param Envelope|object $event
     *
     * @return mixed
     */
    public function __invoke($event)
    {
        $this->eventBus->dispatch($event);
    }
}

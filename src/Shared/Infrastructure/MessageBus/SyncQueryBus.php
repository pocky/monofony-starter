<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\MessageBus;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class SyncQueryBus implements QueryBusInterface
{
    use HandleTrait;

    public function __construct(
        /** @phpstan-ignore-next-line */
        private readonly MessageBusInterface $queryBus,
    ) {
        $this->messageBus = $queryBus;
    }

    /**
     * @param Envelope|object $query
     *
     * @return mixed
     */
    public function __invoke($query)
    {
        return $this->handle($query);
    }
}

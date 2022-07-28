<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\MessageBus;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class SyncCommandBus implements CommandBusInterface
{
    use HandleTrait;

    public function __construct(
        /** @phpstan-ignore-next-line */
        private readonly MessageBusInterface $commandBus,
    ) {
        $this->messageBus = $commandBus;
    }

    /**
     * @param Envelope|object $command
     *
     * @return mixed
     */
    public function __invoke($command)
    {
        return $this->handle($command);
    }
}

<?= "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class Handler implements MessageHandlerInterface
{
    public function __construct(
        private readonly MessageBusInterface $eventBus,
    ) {
    }

    public function __invoke(Command $command): bool
    {
        $this->eventBus->dispatch(
            (new Envelope(new Event('identifier')))
                ->with(new DispatchAfterCurrentBusStamp())
        );
    }
}

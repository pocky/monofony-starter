<?= "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

<?= $use_statements; ?>

final class <?= $class_name; ?> implements MessageHandlerInterface
{
    public function __construct(
<?php foreach ($constructor_arguments as $argument): ?>
        private readonly <?= $argument['short_name'] ?> $<?= $argument['argument_name'] ?>,
<?php endforeach; ?>    ) {
    }

    public function __invoke(Command $command): <?= $domain_return_type . "\n" ?>
    {
        $this-><?= $domain_argument ?>-><?= $domain_method ?>($command->getId());

<?php if ([] !== $event): ?>
        $this->eventBus->dispatch(
            (new Envelope(new <?= $event['name'] ?>($command->getId()->getValue())))
                ->with(new DispatchAfterCurrentBusStamp())
        );
<?php endif; ?>
    }
}

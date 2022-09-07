<?= "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

<?= $use_statements; ?>

final class <?= $class_name . "\n"; ?>
{
    public function __construct(
        private readonly ErrorHandler $errorHandler,
        private readonly Logger $logger,
        private readonly Processor $processor
    ) {
    }

    public function __invoke(GatewayRequest $request): GatewayResponse
    {
        return (new Pipe([
            $this->logger,
            $this->errorHandler,
            $this->processor,
        ]))($request);
    }
}

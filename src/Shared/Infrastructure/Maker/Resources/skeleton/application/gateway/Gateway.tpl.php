<?= "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

use <?= $namespace; ?>\Middleware\ErrorHandler;
use <?= $namespace; ?>\Middleware\Logger;
use <?= $namespace; ?>\Middleware\Processor;
use App\Shared\Application\Gateway\GatewayRequest;
use App\Shared\Application\Gateway\GatewayResponse;
use App\Shared\Application\Gateway\Middleware\Pipe;

final class Gateway
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

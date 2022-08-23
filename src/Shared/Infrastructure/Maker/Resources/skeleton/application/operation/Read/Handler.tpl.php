<?= "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class Handler implements MessageHandlerInterface
{
    public function __construct(
    ) {
    }

    public function __invoke(Query $query): array
    {
    }
}

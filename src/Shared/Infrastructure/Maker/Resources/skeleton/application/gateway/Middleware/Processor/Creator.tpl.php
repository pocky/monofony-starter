<?php

use App\Shared\Infrastructure\MessageBus\CommandBusInterface;
use App\Shared\Infrastructure\MessageBus\QueryBusInterface;
use Symfony\Bundle\MakerBundle\Str;

?>
<?= "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

<?= $use_statements ?>

final class <?= $class_name . "\n" ?>
{
    public function __construct(
        private readonly <?= $generator ?> $identityGenerator,
        private readonly CommandBusInterface $commandBus,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $identity = $this->identityGenerator->nextIdentity();
        ($this->commandBus)(new Command(
            $identity,
<?php foreach ($request_parameters['required'] as $key => $value): ?>
            $request->get<?= Str::asCamelCase($key) ?>(),
<?php endforeach; ?>
<?php foreach ($request_parameters['optional'] as $key => $value): ?>
            $request->get<?= Str::asCamelCase($key) ?>(),
<?php endforeach; ?>
        ));

        return new Response($identity);
    }
}

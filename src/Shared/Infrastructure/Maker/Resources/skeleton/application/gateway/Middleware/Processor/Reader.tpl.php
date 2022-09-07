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
        private readonly QueryBusInterface $queryBus,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        return new Response(
            ($this->queryBus)(new Query(
<?php foreach ($request_parameters['required'] as $key => $value): ?>
                $request->get<?= Str::asCamelCase($key) ?>(),
<?php endforeach; ?>
<?php foreach ($request_parameters['optional'] as $key => $value): ?>
                $request->get<?= Str::asCamelCase($key) ?>(),
<?php endforeach; ?>
        ));
    }
}

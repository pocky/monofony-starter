<?php declare(strict_types=1);

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
        $data = ($this->queryBus)(new Query(
<?php foreach ($request_parameters['required'] as $key => $value): ?>
            $request->get<?= Str::asCamelCase($key) ?>(),
<?php endforeach; ?>
<?php foreach ($request_parameters['optional'] as $key => $value): ?>
            $request->get<?= Str::asCamelCase($key) ?>(),
<?php endforeach; ?>
        ));

        $response = new Response();
        foreach ($data as $value) {
            $response->add($value);
        }

        return $response;
    }
}

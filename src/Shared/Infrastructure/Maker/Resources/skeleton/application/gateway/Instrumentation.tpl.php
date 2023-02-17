<?= "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

<?= $use_statements ?>

final class <?= $class_name ?> extends AbstractGatewayInstrumentation
{
    public const NAME = '<?= $event_name; ?>';
}

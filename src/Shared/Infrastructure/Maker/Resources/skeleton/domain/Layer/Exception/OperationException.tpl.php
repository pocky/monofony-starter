<?= "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

<?= $use_statements ?>

final class <?= $class_name ?> extends \Exception
{
    public function __construct(<?= $identifier_name ?> $id, \Exception $previous = null)
    {
        parent::__construct(
            message: sprintf('Error during process %s with identifier %s', <?= $model_name ?>::class, $id->getValue()),
            previous: $previous,
        );
    }
}

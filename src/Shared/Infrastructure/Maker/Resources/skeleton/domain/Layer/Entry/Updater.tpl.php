<?= "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

<?= $use_statements ?>

final class <?= "$class_name\n" ?>
{
    public function __construct(
<?php foreach ($constructor_arguments as $argument): ?>
        private readonly <?= $argument['class_name'] ?> $<?= $argument['argument_name'] ?>,
<?php endforeach; ?>
    ) {
    }

    public function <?= $entry_method ?>(<?= $entry_model ?> $model): void
    {
        try {
            $this->persister-><?= $entry_method ?>($model);
        } catch (\Exception $exception) {
            throw new <?= $exception ?>($model->getId(), $exception);
        }
    }
}

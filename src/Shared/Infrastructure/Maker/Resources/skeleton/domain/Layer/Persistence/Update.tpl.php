<?= "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

<?= $use_statements ?>

interface <?= "$class_name\n" ?>
{
    public function <?= $entry_method ?>(<?= $model_name ?> $model): void;
}

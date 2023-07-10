<?= "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

<?= $use_statements ?>

final class <?= $class_name . "\n"; ?>
{
    public function __construct(
        private readonly <?= $identifier_name ?> $id,
<?php foreach ($fields as $field): ?>
        private readonly <?= $field['type']; ?> $<?= $field['fieldName']; ?>,
<?php endforeach; ?>
    ) {
    }

    public function getId(): <?= $identifier_name . "\n" ?>
    {
        return $this->id;
    }

<?php $i = 0;
$number = is_countable($fields) ? count($fields) : 0; ?>
<?php foreach ($fields as $field): ?>
    public function get<?= ucfirst((string) $field['fieldName']); ?>(): <?= $field['type'] . "\n"; ?>
    {
        return $this-><?= $field['fieldName']; ?>;
    }
<?php $i++; ?>
<?php if ($number > 1 && $i < $number): ?>
<?= "\n"; ?>
<?php endif; ?>
<?php endforeach; ?>
}

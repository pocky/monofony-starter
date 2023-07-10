<?php declare(strict_types=1);

use Symfony\Bundle\MakerBundle\Str;

?>
<?= "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

<?= $use_statements ?>

final class <?= $class_name . "\n" ?>
{
<?php $i = 0;
$number = is_countable($fields) ? count($fields) : 0; ?>

<?php if (0 !== $number): ?>
    public function __construct(
<?php foreach ($fields as $field): ?>
        private readonly <?= $field['argument_type']; ?> $<?= Str::asLowerCamelCase(str_replace('get', '', (string) $field['fieldName'])); ?>,
<?php endforeach; ?>
    ) {
    }
<?php endif; ?>

<?php foreach ($fields as $field): ?>
<?php if ('getId' === $field['fieldName']): ?>
    public function <?= $field['fieldName']; ?>(): <?= $field['short_name'] . "\n"; ?>
    {
        return new <?= $field['short_name'] ?>($this-><?= Str::asLowerCamelCase(str_replace('get', '', $field['fieldName'])); ?>);
    }

<?php continue; ?>
<?php endif; ?>
    public function <?= $field['fieldName']; ?>(): <?= $field['short_name'] . "\n"; ?>
    {
        return $this-><?= Str::asLowerCamelCase(str_replace('get', '', (string) $field['fieldName'])); ?>;
    }
<?php $i++; ?>
<?php if ($number > 1 && $i < $number): ?>
<?= "\n"; ?>
<?php endif; ?>
<?php endforeach; ?>
}

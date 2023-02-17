<?php declare(strict_types=1);

use Symfony\Bundle\MakerBundle\Str;

?>
<?= "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

<?= $use_statements; ?>

final class <?= $class_name; ?> implements GatewayResponse
{
    public function __construct(
<?php foreach ($response_parameters['required'] as $key => $value): ?>
        private readonly <?= $value ?> $<?= $key ?>,
<?php endforeach; ?>
<?php foreach ($response_parameters['optional'] as $key => $value): ?>
        private readonly ?<?= $value ?> $<?= $key ?>,
<?php endforeach; ?>
    ) {
    }

<?php foreach ($response_parameters['required'] as $key => $value): ?>
    public function get<?= Str::asCamelCase($key) ?>(): <?= $value . "\n" ?>
    {
        return $this-><?= $key ?>;
    }

<?php endforeach; ?>
<?php foreach ($response_parameters['optional'] as $key => $value): ?>
    public function get<?= Str::asCamelCase($key) ?>(): ?<?= $value . "\n" ?>
    {
        return $this-><?= $key ?>;
    }

<?php endforeach; ?>
    public function data(): array
    {
        return [
<?php foreach ($response_parameters['required'] as $key => $value): ?>
<?php if ('id' === $key): ?>
            '<?= $key ?>' => $this->get<?= Str::asCamelCase($key) ?>()->getValue(),
<?php else: ?>
            '<?= $key ?>' => $this->get<?= Str::asCamelCase($key) ?>(),
<?php endif; ?>
<?php endforeach; ?>
<?php foreach ($response_parameters['optional'] as $key => $value): ?>
            '<?= $key ?>' => $this->get<?= Str::asCamelCase($key) ?>(),
<?php endforeach; ?>
        ];
    }
}

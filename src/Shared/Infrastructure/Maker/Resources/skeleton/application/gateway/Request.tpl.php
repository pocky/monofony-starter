<?php declare(strict_types=1);

use Symfony\Bundle\MakerBundle\Str;

?>
<?= "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

<?= $use_statements; ?>

final class <?= $class_name; ?> implements GatewayRequest
{
<?php foreach ($request_parameters['required'] as $key => $value): ?>
    private <?= $value ?> $<?= $key ?>;

<?php endforeach; ?>
<?php foreach ($request_parameters['optional'] as $key => $value): ?>
    private ?<?= $value ?> $<?= $key ?> = null;

<?php endforeach; ?>
    public static function fromData(array $data = []): self
    {
        $dto = new self();
        $requiredFields = [
<?php foreach ($request_parameters['required'] as $key => $value): ?>
            '<?= $key ?>',
<?php endforeach; ?>
        ];
<?php if ([] !== $request_parameters['optional']): ?>
        $optionalFields = [
<?php foreach ($request_parameters['optional'] as $key => $value): ?>
            '<?= $key ?>',
<?php endforeach; ?>
        ];
<?php endif; ?>

        $accessor = PropertyAccess::createPropertyAccessor();
        foreach ($requiredFields as $field) {
            $dto->{$field} = $accessor->getValue($data, "[{$field}]");
        }
<?php if ([] !== $request_parameters['optional']): ?>
        foreach ($optionalFields as $field) {
            if (true === isset($data[$field])) {
                $dto->{$field} = $accessor->getValue($data, "[{$field}]");
            }
        }
<?php endif; ?>
        return $dto;
    }

<?php foreach ($request_parameters['required'] as $key => $value): ?>
    public function get<?= Str::asCamelCase($key) ?>(): <?= $value . "\n" ?>
    {
        return $this-><?= $key ?>;
    }

<?php endforeach; ?>
<?php foreach ($request_parameters['optional'] as $key => $value): ?>
    public function get<?= Str::asCamelCase($key) ?>(): ?<?= $value . "\n" ?>
    {
        return $this-><?= $key ?>;
    }

<?php endforeach; ?>
    public function data(): array
    {
        return [
<?php foreach ($request_parameters['required'] as $key => $value): ?>
            '<?= $key ?>' => $this->get<?= Str::asCamelCase($key) ?>(),
<?php endforeach; ?>
<?php foreach ($request_parameters['optional'] as $key => $value): ?>
            '<?= $key ?>' => $this->get<?= Str::asCamelCase($key) ?>(),
<?php endforeach; ?>
        ];
    }
}

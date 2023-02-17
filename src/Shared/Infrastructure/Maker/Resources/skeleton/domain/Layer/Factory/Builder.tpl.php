<?= "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

<?= $use_statements ?>

final class Builder
{
    public static function build(array $data = []): <?= $model_name . "\n"; ?>
    {
        Assert::notEmpty($data);
<?php foreach ($properties as $property): ?>
        Assert::keyExists($data, '<?= $property ?>');
<?php endforeach ?>

        return new <?= $model_name; ?>(
<?php foreach ($properties as $property): ?>
            $data['<?= $property ?>'],
<?php endforeach ?>
        );
    }
}

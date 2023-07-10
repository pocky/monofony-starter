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

    public function <?= $entry_method ?>(): <?= $entry_model . "\n" ?>
    {
        $collection = new <?= $entry_model ?>();
        $results = $this->provider-><?= $entry_method ?>();

        foreach ($results as $result) {
            $collection->add($this->builder::build([
<?php foreach ($properties as $property): ?>
<?php if ('id' === $property): ?>
                '<?= $property ?>' => new <?= $identifier ?>($result->getId()),
<?php else: ?>
                '<?= $property ?>' => $result->get<?= ucfirst((string) $property) ?>(),
<?php endif; ?>
<?php endforeach; ?>
            ]));
        }

        return $collection;
    }
}

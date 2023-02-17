<?= "<?php\n"; ?>

namespace <?= $namespace; ?>;

<?= $use_statements; ?>

class <?= $class_name; ?> implements FactoryInterface
{
    public function __construct(
        private readonly string $className,
        private readonly <?= $generator_name; ?> $generator
    ) {
    }

    public function createNew()
    {
        return new $this->className(
            $this->generator->nextIdentity(),
        );
    }
}

<?= "<?php\n"; ?>

namespace <?= $namespace; ?>;

<?= $use_statements; ?>

class <?= "$class_name\n"; ?>
{
    public function __construct(
        private readonly GeneratorInterface $generator
    ) {
    }

    public function nextIdentity()
    {
        return new <?= $identifier_name; ?>($this->generator::generate());
    }
}

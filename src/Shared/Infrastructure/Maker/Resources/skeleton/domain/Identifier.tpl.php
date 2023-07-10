<?= "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

final class <?= "$class_name\n"; ?>
{
    public function __construct(
        private readonly string $value,
    ) {
    }

    public function getValue(): string
    {
        return $this->value;
    }
}

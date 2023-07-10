<?php declare(strict_types=1);
$inflector = new Symfony\Component\String\Inflector\EnglishInflector();
$list_name = mb_strtolower((string) $list_name);
$pluralize_list_name = $inflector->pluralize($list_name)[0];
?>
<?= "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

final class <?= $class_name . "\n"; ?>
{
    public function __construct(
        private array $<?= $pluralize_list_name; ?> = [],
    ) {
    }

    public function add(<?= ucfirst($list_name); ?> $<?= $list_name; ?>): void
    {
        if (!$this->contains($<?= $list_name; ?>)) {
            $this-><?= $pluralize_list_name; ?>[] = $<?= $list_name; ?>;
        }
    }

    public function contains(<?= ucfirst($list_name); ?> $<?= $list_name; ?>): bool
    {
        return in_array($<?= $list_name; ?>, $this-><?= $pluralize_list_name; ?>, true);
    }

    public function get<?= ucfirst($pluralize_list_name); ?>(): array
    {
        return $this-><?= $pluralize_list_name; ?>;
    }
}

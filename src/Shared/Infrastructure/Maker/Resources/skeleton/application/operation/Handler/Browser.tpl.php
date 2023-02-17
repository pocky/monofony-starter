<?= "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

<?= $use_statements; ?>

final class <?= $class_name; ?> implements MessageHandlerInterface
{
    public function __construct(
<?php foreach ($constructor_arguments as $argument): ?>
        private readonly <?= $argument['short_name'] ?> $<?= $argument['argument_name'] ?>,
<?php endforeach; ?>    ) {
    }

    public function __invoke(Query $query): <?= $domain_return_type . "\n" ?>
    {
        return $this-><?= $domain_argument ?>-><?= $domain_method ?>();
    }
}

<?= "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

<?= $use_statements; ?>
use Behat\Behat\Context\Context;
use Monofony\Bridge\Behat\Service\SharedStorageInterface;

final class <?= $class_name; ?> implements Context
{
    public function __construct(
        private readonly SharedStorageInterface $sharedStorage,
    ) {
    }

    /**
     * @Given I already have :identifier <?= $entity_name . "\n"; ?>
     * @Given I already have <?= $entity_name . "\n"; ?>
     * @When There are :identifier <?= $entity_name . "\n"; ?>
     */
    public function iAlreadyHaveResource($identifier = null): void
    {
        if (null !== $identifier) {
            $resource = <?= $entity_factory_class; ?>::createOne(['<?= $entity_identifier; ?>' => $identifier]);
        } else {
            $resource = <?= $entity_factory_class; ?>::createOne();
        }

        $this->sharedStorage->set('<?= $entity_name; ?>', $resource->object());
    }

    /**
     * @Given I already have :number <?= $entity_name; ?>s
     * @Given I already have <?= $entity_name; ?>s
     * @When There are :number <?= $entity_name; ?>s
     */
    public function iAlreadyHaveResources(int $number = 2): void
    {
        <?= $entity_factory_class; ?>::createMany($number);
    }
}

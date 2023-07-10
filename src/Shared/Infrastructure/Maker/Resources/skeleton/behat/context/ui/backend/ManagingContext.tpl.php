<?= "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

<?= $use_statements; ?>
use Behat\Behat\Context\Context;
use Webmozart\Assert\Assert;

final class <?= $class_name; ?> implements Context
{
    public function __construct(
        private readonly IndexPage  $indexPage,
        private readonly CreatePage $createPage,
        private readonly UpdatePage $updatePage,
    ) {
    }

    /**
     * @Given I want to browse <?= $entity_name; ?>s
     * @When I am browsing <?= $entity_name; ?>s
     */
    public function iWantToBrowseResources(): void
    {
        $this->indexPage->open();
    }

    /**
     * @Given I want to create a new <?= $entity_name . "\n"; ?>
     * @Then I am creating a new <?= $entity_name . "\n"; ?>
     */
    public function iWantToCreateResource(): void
    {
        $this->createPage->open();
    }

    /**
     * @Given /^I want to delete (this <?= $entity_name; ?>)$/
     * @Then /^I am deleting (this <?= $entity_name; ?>)$/
     */
    public function iWantToDeleteThisResource(<?= ucfirst((string) $entity_name); ?> $resource): void
    {
        $this->indexPage->open();
        $this->indexPage->delete($resource->get<?= ucfirst((string) $entity_identifier); ?>());
    }

    /**
     * @When I delete them
     */
    public function iDeleteThem(): void
    {
        $this->indexPage->bulkDelete();
    }

    /**
     * @Given /^I want to update (this <?= $entity_name; ?>)$/
     * @Then /^I am updating (this <?= $entity_name; ?>)$/
     */
    public function iWantToUpdateThisResource(<?= ucfirst((string) $entity_name); ?> $resource): void
    {
        $this->updatePage->open(['id' => $resource->getId()]);
    }

    /**
     * @When I save my changes
     */
    public function iSaveIt(): void
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @When I add it
     */
    public function iAddIt(): void
    {
        $this->createPage->create();
    }

    /**
     * @When I set its :field to :value
     */
    public function iSetItsFieldTo(string $field, string $value): void
    {
        $this->createPage->setFieldTo($field, $value);
    }

    /**
     * @When I change its :field to :value
     */
    public function iChangeItsFieldTo(string $field, string $value): void
    {
        $this->updatePage->setFieldTo($field, $value);
    }

    /**
     * @When I check the :identifier <?= $entity_name; ?>
     */
    public function iCheckTheResource(string $identifier): void
    {
        $this->indexPage->checkResource($identifier);
    }

    /**
     * @Then I should see <?= $entity_name; ?>s in the list
     * @Then I should see :number <?= $entity_name; ?>s in the list
     */
    public function iShouldSeeResources(int $number = 2): void
    {
        Assert::eq($this->indexPage->countResources(), $number);
    }

    /**
     * @Then I should see the <?= $entity_name; ?> :identifier in the list
     */
    public function iShouldSeeResourceWithIdentifier(string $identifier): void
    {
        Assert::true($this->indexPage->hasResource($identifier));
    }

    /**
     * @Then I should not see the :identifier <?= $entity_name; ?> in the list
     */
    public function iShouldNotSeeResourceWithIdentifier(string $identifier): void
    {
        Assert::true($this->indexPage->hasNotResource($identifier));
    }
}

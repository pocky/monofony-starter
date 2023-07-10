<?= "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

<?= $use_statements; ?>
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Behat\Behat\Context\Context;
use Monofony\Bridge\Behat\Client\ApiClientInterface;
use Webmozart\Assert\Assert;

final class <?= $class_name; ?> extends ApiTestCase implements Context
{
    const RESOURCE_NAME = '<?= $entity_name; ?>s';

    public function __construct(
        private readonly ApiClientInterface $client,
    ) {
    }

    /**
     * @Given I want to browse <?= $entity_name; ?>s
     * @When I am browsing <?= $entity_name; ?>s
     */
    public function iWantToBrowseResources(): void
    {
        $this->client->index(self::RESOURCE_NAME);
    }

    /**
     * @Given I want to create a new <?= $entity_name . "\n"; ?>
     * @When I am creating a new <?= $entity_name . "\n"; ?>
     */
    public function iWantToCreateResource(): void
    {
        $this->client->buildCreateRequest(self::RESOURCE_NAME);
    }

    /**
     * @Given /^I want to delete (this <?= $entity_name; ?>)$/
     * @When /^I am deleting (this <?= $entity_name; ?>)$/
     */
    public function iWantToDeleteThisResource(<?= ucfirst((string) $entity_name); ?> $resource): void
    {
        $this->client->delete(self::RESOURCE_NAME, $resource->getId());
    }

    /**
     * @Given /^I want to update (this <?= $entity_name; ?>)$/
     * @When /^I am updating (this <?= $entity_name; ?>)$/
     */
    public function iWantToUpdateThisResource(<?= ucfirst((string) $entity_name); ?> $resource): void
    {
        $this->client->buildUpdateRequest(self::RESOURCE_NAME, $resource->getId());
    }

    /**
     * @When I add it
     * @When I save my changes
     */
    public function iAddIt(): void
    {
        $this->client->create();
    }

    /**
     * @When I set its :field to :value
     * @When I change its :field to :value
     */
    public function iSetItsFieldTo(string $field, string $value): void
    {
        $this->client->addRequestData($field, $value);
    }

    /**
     * @Then I should see <?= $entity_name; ?>s in the list
     * @Then I should see :number <?= $entity_name; ?>s in the list
     */
    public function iShouldSeeResources(int $number = 2): void
    {
        $response = $this->client->getLastResponse();
        $content = json_decode($response->getContent(), true);

        Assert::eq($content['hydra:totalItems'], $number);
    }

    /**
     * @Then I should see the <?= $entity_name; ?> :identifier in the list
     */
    public function iShouldSeeResourceWithIdentifier(string $identifier): void
    {
        $response = $this->client->index(self::RESOURCE_NAME);
        $content = json_decode($response->getContent(), true);

        $index = array_search(
            $identifier,
            array_column($content['hydra:member'], '<?= $entity_identifier; ?>'),
        );

        Assert::notNull($index);
    }

    /**
     * @Then I should not see the :identifier <?= $entity_name; ?> in the list
     */
    public function iShouldNotSeeResourceWithIdentifier(string $identifier): void
    {
        $response = $this->client->index(self::RESOURCE_NAME);
        $content = json_decode($response->getContent(), true);

        $index = array_search(
            $identifier,
            array_column($content['hydra:member'], '<?= $entity_identifier; ?>'),
        );

        Assert::false($index);
    }
}

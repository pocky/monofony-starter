<?= "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

use App\Tests\Behat\Shared\Page\AbstractIndexPage;

final class <?= $class_name; ?> extends AbstractIndexPage
{
    public function getRouteName(): string
    {
        return '<?= $entity_route; ?>';
    }

    public function countResources(): int
    {
        return $this->countItems();
    }

    public function hasResource(string $identifier): bool
    {
        return $this->isSingleResourceOnPage(['<?= $entity_identifier; ?>' => $identifier]);
    }

    public function delete(string $identifier): void
    {
        $this->deleteResourceOnPage(['<?= $entity_identifier; ?>' => $identifier]);
    }

    public function checkResource(string $identifier): void
    {
        $this->checkResourceOnPage(['<?= $entity_identifier; ?>' => $identifier]);
    }

    public function hasNotResource(string $identifier): bool
    {
        return !$this->isSingleResourceOnPage(['<?= $entity_identifier; ?>' => $identifier]);
    }
}

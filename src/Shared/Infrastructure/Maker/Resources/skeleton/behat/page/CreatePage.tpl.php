<?= "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

use App\Tests\Behat\Shared\Behaviour\SetField;
use App\Tests\Behat\Shared\Page\AbstractCreatePage;

final class <?= $class_name; ?> extends AbstractCreatePage
{
    use SetField;

    public function getRouteName(): string
    {
        return '<?= $entity_route; ?>';
    }

    public function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
<?php foreach ($defined_elements as $key => $value): ?>
            '<?= $key ?>' => <?= $value ?>,
<?php endforeach; ?>
        ]);
    }
}

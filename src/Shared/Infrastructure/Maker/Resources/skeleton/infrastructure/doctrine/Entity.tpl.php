<?= "<?php\n" ?>

namespace <?= $namespace ?>;

<?= $use_statements; ?>

#[ORM\Entity(repositoryClass: <?= $repository_class_name ?>::class)]
#[ORM\Table(name: '<?= $table_name ?>')]
<?php if ($api_resource): ?>
#[Metadata\ApiResource(
    operations: [
        new Metadata\GetCollection(),
        new Metadata\Get(),
        new Metadata\Put(),
        new Metadata\Post(),
        new Metadata\Patch(),
        new Metadata\Delete(),
    ],
)]
<?php endif ?>
<?php if ($sylius_crud): ?>
#[SyliusCrudRoutes(
    alias: '<?= $crud_route_alias ?>',
    path: '/admin/<?= $crud_route_path ?>',
    section: 'backend',
    redirect: 'index',
    templates: 'backend/crud',
    grid: 'app_backend_<?= $crud_route_grid ?>',
    except: ['show'],
    vars: [
        'all' => [
            'icon' => 'users',
            'subheader' => 'backend.<?= $crud_route_package ?>.ui.<?= $crud_route_entity ?>.subheader',
        ],
        'index' => [
            'header' => 'backend.<?= $crud_route_package ?>.ui.<?= $crud_route_entity ?>.index.title',
        ],
        'create' => [
            'header' => 'backend.<?= $crud_route_package ?>.ui.<?= $crud_route_entity ?>.create.title',
        ],
        'update' => [
            'header' => 'backend.<?= $crud_route_package ?>.ui.<?= $crud_route_entity ?>.update.title',
        ],
    ],
)]
<?php endif ?>
class <?= $class_name ?> <?php if ($sylius_crud): ?>implements ResourceInterface<?php endif ?><?= "\n" ?>
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\Column(type: Types::GUID)]
    private string $id;

    public function __construct(<?= $identifier_name; ?> $id)
    {
        $this->id = $id->getValue();
    }

    public function getId(): string
    {
        return $this->id;
    }
}

<?php

declare(strict_types=1);

use App\Kernel;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'assets' => [
            'json_manifest_path' => '%kernel.project_dir%/public/assets/backend/manifest.json',
            'packages' => [
                'backend' => [
                    'json_manifest_path' => '%kernel.project_dir%/public/assets/backend/manifest.json',
                ],
            ],
        ],
    ]);

    $containerConfigurator->extension('sylius_grid', [
        'templates' => [
            'action' => [
                'default' => '@SyliusUi/Grid/Action/default.html.twig',
                'create' => '@SyliusUi/Grid/Action/create.html.twig',
                'delete' => '@SyliusUi/Grid/Action/delete.html.twig',
                'show' => '@SyliusUi/Grid/Action/show.html.twig',
                'update' => '@SyliusUi/Grid/Action/update.html.twig',
                'apply_transition' => '@SyliusUi/Grid/Action/applyTransition.html.twig',
                'links' => '@SyliusUi/Grid/Action/links.html.twig',
                'archive' => '@SyliusUi/Grid/Action/archive.html.twig',
            ],
            'bulk_action' => [
                'delete' => '@SyliusUi/Grid/BulkAction/delete.html.twig',
            ],
            'filter' => [
                'string' => '@SyliusUi/Grid/Filter/string.html.twig',
                'boolean' => '@SyliusUi/Grid/Filter/boolean.html.twig',
                'date' => '@SyliusUi/Grid/Filter/date.html.twig',
                'entity' => '@SyliusUi/Grid/Filter/entity.html.twig',
                'money' => '@SyliusUi/Grid/Filter/money.html.twig',
                'exists' => '@SyliusUi/Grid/Filter/exists.html.twig',
                'select' => '@SyliusUi/Grid/Filter/select.html.twig',
            ],
        ],
    ]);

    $containerConfigurator->extension('sonata_block', [
        'blocks' => [
            'sonata.block.service.template' => [
                'settings' => [
                    'form' => null,
                    'resource' => null,
                    'resources' => null,
                ],
            ],
        ],
    ]);

    $containerConfigurator->extension('twig', [
        'globals' => [
            'app_name_meta' => [
                'version' => Kernel::VERSION,
            ],
        ],
    ]);
};

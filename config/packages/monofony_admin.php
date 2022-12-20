<?php

declare(strict_types=1);

use App\Kernel;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import('@SyliusUiBundle/Resources/config/app/config.yml');

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

    $containerConfigurator->extension('sylius_ui', [
        'events' => [
            'sylius.admin.layout.topbar_left' => [
                'blocks' => [
                    'sidebar_toggle' => [
                        'template' => 'backend/layout/_sidebar_toggle.html.twig',
                        'priority' => 30,
                    ],
                    'search' => [
                        'template' => 'backend/layout/_search.html.twig',
                        'priority' => 10,
                    ],
                ],
            ],
            'sylius.admin.layout.topbar_right' => [
                'blocks' => [
                    'security' => [
                        'template' => 'backend/layout/_security.html.twig',
                        'priority' => 10,
                    ],
                ],
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

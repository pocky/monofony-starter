<?php

declare(strict_types=1);

use App\Shared\Infrastructure\Doctrine\ORM\Entity\User\AdminUser;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('sylius_grid', [
        'grids' => [
            'sylius_backend_admin_user' => [
                'driver' => [
                    'name' => 'doctrine/orm',
                    'options' => [
                        'class' => AdminUser::class,
                    ],
                ],
                'sorting' => [
                    'email' => 'desc',
                ],
                'fields' => [
                    'firstName' => [
                        'type' => 'string',
                        'label' => 'sylius.ui.first_name',
                        'sortable' => null,
                    ],
                    'lastName' => [
                        'type' => 'string',
                        'label' => 'sylius.ui.last_name',
                        'sortable' => null,
                    ],
                    'username' => [
                        'type' => 'string',
                        'label' => 'sylius.ui.username',
                        'sortable' => null,
                    ],
                    'email' => [
                        'type' => 'string',
                        'label' => 'sylius.ui.email',
                        'sortable' => null,
                    ],
                    'enabled' => [
                        'type' => 'twig',
                        'label' => 'sylius.ui.enabled',
                        'sortable' => null,
                        'options' => [
                            'template' => '@SyliusUi/Grid/Field/enabled.html.twig',
                        ],
                    ],
                ],
                'filters' => [
                    'search' => [
                        'type' => 'string',
                        'label' => 'sylius.ui.search',
                        'options' => [
                            'fields' => ['email', 'username', 'firstName', 'lastName'],
                        ],
                    ],
                ],
                'actions' => [
                    'main' => [
                        'create' => ['type' => 'create'],
                    ],
                    'item' => [
                        'update' => ['type' => 'update'],
                        'delete' => ['type' => 'delete'],
                    ],
                    'bulk' => [
                        'delete' => ['type' => 'delete'],
                    ],
                ],
            ],
        ],
    ]);
};

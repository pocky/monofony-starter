<?php

declare(strict_types=1);

use App\Shared\Infrastructure\Doctrine\ORM\Entity\Customer\Customer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('sylius_grid', [
        'grids' => [
            'sylius_backend_customer' => [
                'driver' => [
                    'name' => 'doctrine/orm',
                    'options' => [
                        'class' => Customer::class,
                    ],
                ],
                'sorting' => [
                    'createdAt' => 'desc',
                ],
                'fields' => [
                    'firstName' => [
                        'type' => 'string',
                        'label' => 'sylius.ui.first_name',
                    ],
                    'lastName' => [
                        'type' => 'string',
                        'label' => 'sylius.ui.last_name',
                    ],
                    'email' => [
                        'type' => 'string',
                        'label' => 'sylius.ui.email',
                        'sortable' => true,
                    ],
                    'createdAt' => [
                        'type' => 'datetime',
                        'label' => 'sylius.ui.registration_date',
                        'sortable' => true,
                    ],
                ],
                'filters' => [
                    'search' => [
                        'type' => 'string',
                        'label' => 'sylius.ui.search',
                        'options' => [
                            'fields' => ['email', 'firstName', 'lastName'],
                        ],
                    ],
                ],
                'actions' => [
                    'main' => [
                        'create' => ['type' => 'create'],
                    ],
                    'item' => [
                        'show' => ['type' => 'show'],
                        'update' => ['type' => 'update'],
                    ],
                ],
            ],
        ],
    ]);
};

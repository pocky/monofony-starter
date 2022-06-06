<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routingConfigurator): void {
    $routingConfigurator
        ->add('sylius_backend_partial_customer_latest', '/latest/{count}')
        ->defaults([
            '_controller' => 'sylius.controller.customer::indexAction',
            '_sylius' => [
                'repository' => [
                    'method' => 'findLatest',
                    'arguments' => ['!!int $count'], ],
                'template' => '$template',
            ],
        ])
        ->methods(['GET']);
};

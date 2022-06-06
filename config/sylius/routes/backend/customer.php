<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routingConfigurator): void {
    $routingConfigurator
        ->add('sylius_backend_customer_show', '/customers/{id}')
        ->defaults([
            '_controller' => 'sylius.controller.customer:showAction',
            '_sylius' => [
                'section' => 'backend',
                'template' => 'backend/customer/show.html.twig',
                'permission' => true,
            ],
        ]);
};

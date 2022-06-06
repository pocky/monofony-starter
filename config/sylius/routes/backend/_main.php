<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routingConfigurator): void {
    $routingConfigurator
        ->add('app_backend_dashboard', '/')
        ->defaults([
            '_controller' => 'App\UI\Backend\Controller\Dashboard\DashboardController:indexAction',
            'template' => 'backend/index.html.twig',
        ]);

    $routingConfigurator
        ->import('partial.php')
        ->prefix('/_partial');

    $routingConfigurator
        ->import('customer.php');

    $routingConfigurator
        ->import('security.php');
};

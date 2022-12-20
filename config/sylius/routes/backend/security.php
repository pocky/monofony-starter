<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routingConfigurator): void {
    $routingConfigurator
        ->add('sylius_backend_login', '/login')
        ->defaults([
            '_controller' => 'sylius.controller.security::loginAction',
            '_sylius' => [
                'template' => 'backend/security/login.html.twig',
                'permission' => true,
            ],
        ]);

    $routingConfigurator
        ->add('sylius_backend_login_check', '/login-check')
        ->defaults([
            '_controller' => 'sylius.controller.security::checkAction',
        ]);

    $routingConfigurator
        ->add('sylius_backend_logout', '/logout');
};

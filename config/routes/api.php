<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routingConfigurator): void {
    $routingConfigurator
        ->add('api_login_check', '/api/authentication_token');

    $routingConfigurator
        ->add('api_refresh_token', '/api/token/refresh');
};

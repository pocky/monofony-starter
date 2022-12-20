<?php

declare(strict_types=1);

use Sylius\Bundle\ResourceBundle\Routing\CrudRoutesAttributesLoader;
use Sylius\Bundle\ResourceBundle\Routing\RoutesAttributesLoader;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routingConfigurator): void {
    $routingConfigurator->import(CrudRoutesAttributesLoader::class, 'service');

    $routingConfigurator->import(RoutesAttributesLoader::class, 'service');
};

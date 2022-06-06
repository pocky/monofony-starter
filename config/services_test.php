<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/../vendor/monofony/behat-bridge/services_test.yaml');

    $containerConfigurator->parameters()
        ->set('locale', 'en_US');

    $services = $containerConfigurator->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
            ->bind('$minkParameters', service('behat.mink.parameters'))
    ;

    $services->load('App\\Tests\\Behat\\', __DIR__ . '/../tests/Behat/*');
};

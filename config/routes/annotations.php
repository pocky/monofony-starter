<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routingConfigurator): void {
    $routingConfigurator->import('../../src/UI/Backend/Controller/', 'annotation');

    $routingConfigurator->import('../../src/Kernel.php', 'annotation');
};

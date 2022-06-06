<?php

declare(strict_types=1);

use App\Kernel;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('api_platform', [
        'version' => Kernel::VERSION,
        'swagger' => [
            'versions' => [3],
            'api_keys' => [
                'apiKey' => [
                    'name' => 'Authorization',
                    'type' => 'header',
                ],
            ],
        ],
    ]);

    $containerConfigurator->extension('framework', [
        'serializer' => [
            'enabled' => true,
            'mapping' => [
                'paths' => [
                    '%kernel.project_dir%/config/serialization/',
                ],
            ],
        ],
    ]);
};

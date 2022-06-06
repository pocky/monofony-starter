<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('webpack_encore', [
        'output_path' => '%kernel.project_dir%/public/build',
        'script_attributes' => [
            'defer' => true,
            'preload' => true,
        ],
        'strict_mode' => true,
        'builds' => [
            'backend' => '%kernel.project_dir%/public/backend/build',
        ],
    ]);

    $containerConfigurator->extension('framework', [
        'assets' => null,
    ]);
};

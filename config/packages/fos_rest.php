<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    //    $containerConfigurator->extension('fos_rest', [
    //        'param_fetcher_listener' => true,
    //        'allowed_methods_listener' => true,
    //        'routing_loader' => true,
    //        'view' => [
    //            'view_response_listener' => true,
    //        ],
    //        'exception' => [
    //            'codes' => [
    //                MyException::class => 403,
    //            ],
    //            'messages' => [
    //                MyException::class => 'Forbidden area'
    //            ],
    //        ],
    //        'format_listener' => [
    //            'rules' => [
    //                '{ path: ^/api, prefer_extension: true, fallback_format: json, priorities: [ json, html ] }'
    //            ]
    //        ],
    //    ]);
};

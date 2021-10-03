<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\SymfonySetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::PATHS, [__DIR__ . '/src', __DIR__ . '/tests']);
    $parameters->set(
        Option::SYMFONY_CONTAINER_XML_PATH_PARAMETER,
        __DIR__ .  '/var/cache/dev/App_KernelDevDebugContainer.xml')
    ;

    $parameters->set(Option::SETS, [
        SetList::CODE_QUALITY_STRICT,
        SetList::CODE_QUALITY,
        SymfonySetList::SYMFONY_52,
        SymfonySetList::SYMFONY_CODE_QUALITY,
        SetList::SAFE_07
    ]);

    $parameters->set(Option::PATHS, [
        __DIR__ . '/src',
    ]);

    $parameters->set(Option::SKIP, [
        __DIR__ . '/src/Migrations/*',
    ]);
};

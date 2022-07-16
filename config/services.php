<?php

declare(strict_types=1);

use App\Shared\Infrastructure\Maker\MakeDomain;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->parameters()
        ->set('locale', 'fr')
        ->set('email_contact', 'contact@example.com')
        ->set('email_name', 'Contact AppName')
        ->set('email_sender', 'no-reply@example.com');

    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure()
            ->bind('$publicDir', '%kernel.project_dir%/public')
            ->bind('$cacheDir', '%kernel.cache_dir%')
            ->bind('$syliusResources', '%sylius.resources%')
            ->bind('$environment', '%kernel.environment%');

    $services
        ->instanceof(AbstractResourceType::class)
        ->autowire(false);

    $services
        ->load('App\\', __DIR__ . '/../src/')
        ->exclude([
            __DIR__ . '/../src/{DependencyInjection,Entity,Migrations,Tests,Kernel.php}',
            __DIR__ . '/../src/Shared/Infrastructure/Persistence/Doctrine/ORM/Entity',
        ]);

    $services
        ->load('App\\UI\\Backend\\Dashboard\\Controller\\',
            __DIR__ . '/../src/UI/Backend/Dashboard/Controller')
        ->tag('controller.service_arguments');
};

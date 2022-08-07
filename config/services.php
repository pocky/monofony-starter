<?php

declare(strict_types=1);

use App\Shared\Infrastructure\Mailer\SymfonyMailer;
use App\Shared\Infrastructure\Maker\Command\DoctrineEntity\Maker as DoctrineEntityMaker;
use App\Shared\Infrastructure\Maker\Command\DoctrineForm\Maker as DoctrineFormMaker;
use App\Shared\Infrastructure\Maker\Command\PackageBuilder\Maker as PackageMaker;
use App\Shared\Infrastructure\Maker\Command\SyliusFactory\Maker as SyliusFactoryMaker;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

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
            __DIR__ . '/../src/Security/Shared/Infrastructure/Persistence/Doctrine/ORM/Entity',
        ]);

    $services
        ->load(
            'App\\Shared\\Infrastructure\\UI\\Backend\\Dashboard\\Controller\\',
            __DIR__ . '/../src/Shared/Infrastructure/UI/Backend/Dashboard/Controller',
        )
        ->tag('controller.service_arguments');

    $services
        ->set(SymfonyMailer::class)
        ->args([
            '$senderEmail' => '%email_sender%',
            '$senderName' => '%email_name%',
        ])
    ;

    $services
        ->set(DoctrineEntityMaker::class)
        ->args([
            service('maker.file_manager'),
            service('maker.doctrine_helper'),
        ]);

    $services
        ->set(DoctrineFormMaker::class)
        ->args([
            service('maker.doctrine_helper'),
            service('maker.renderer.form_type_renderer'),
            service('maker.file_manager'),
        ]);

    $services
        ->set(PackageMaker::class)
        ->args([
            '%kernel.project_dir%',
            service('maker.file_manager'),
        ]);

    $services
        ->set(SyliusFactoryMaker::class)
        ->args([
            service('maker.file_manager'),
        ]);
};

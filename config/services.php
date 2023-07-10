<?php

declare(strict_types=1);

use App\Security\Infrastructure\OpenApi\Factory\AppAuthenticationTokenOpenApiFactory;
use App\Shared\Infrastructure\Mailer\SymfonyMailer;
use App\Shared\Infrastructure\Maker\Command\Doctrine\Entity\Maker as DoctrineEntityMaker;
use App\Shared\Infrastructure\Maker\Command\Doctrine\Form\Maker as DoctrineFormMaker;
use App\Shared\Infrastructure\Maker\Command\PackageBuilder\Maker as PackageMaker;
use App\Shared\Infrastructure\Maker\Command\Sylius\Factory\Maker as SyliusFactoryMaker;
use App\Shared\Infrastructure\Maker\Renderer\FormTypeRenderer;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Notifier\FlashMessage\BootstrapFlashMessageImportanceMapper;
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
            __DIR__ . '/../src/Security/Shared/Infrastructure/Persistence/Doctrine/ORM/Entity',
            __DIR__ . '/../src/UI/**/Controller/**/Form/*DTO.php',
            __DIR__ . '/../src/**/Sylius/Factory/*Factory.php',
        ]);

    $services
        ->load(
            'App\\Shared\\Infrastructure\\UI\\Backend\\Dashboard\\Controller\\',
            __DIR__ . '/../src/Shared/Infrastructure/UI/Backend/Dashboard/Controller',
        )
        ->tag('controller.service_arguments');

    $services
        ->set('notifier.flash_message_importance_mapper', BootstrapFlashMessageImportanceMapper::class)
    ;

    $services
        ->set('openapi.factory', AppAuthenticationTokenOpenApiFactory::class)
        ->args([
            service('openapi.factory.inner'),
        ])
        ->decorate('api_platform.openapi.factory')
        ->autowire(false);

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

    $services
        ->set('maker.renderer.form_type_renderer')
        ->class(FormTypeRenderer::class)
        ->args([
            service('maker.generator'),
        ])
    ;
};

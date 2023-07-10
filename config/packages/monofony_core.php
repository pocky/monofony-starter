<?php

declare(strict_types=1);

use App\Security\Infrastructure\Persistence\Doctrine\ORM\Entity\User\AdminUser;
use App\Security\Infrastructure\UI\Backend\AdminUser\Form\Type\AdminUserType;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\UserBundle\Doctrine\ORM\UserRepository as SyliusUserRepository;
use Sylius\Component\User\Model\UserOAuth;
use Sylius\Component\User\Model\UserOAuthInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Vich\UploaderBundle\Naming\OrignameNamer;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import('../sylius/resources.php');
    $containerConfigurator->import('@SyliusUserBundle/Resources/config/app/config.yml');

    $containerConfigurator->extension('framework', [
        'translator' => [
            'default_path' => '%kernel.project_dir%/translations',
            'fallbacks' => ['%locale%'],
        ],
    ]);

    $containerConfigurator->extension('liip_imagine', [
        'loaders' => [
            'default' => [
                'filesystem' => [
                    'locator' => 'filesystem_insecure',
                    'data_root' => ['%kernel.project_dir%/public'],
                ],
            ],
        ],
        'filter_sets' => [
            'cache' => null,
            'default' => [
                'quality' => 100,
                'filters' => [
                    'auto_rotate' => null,
                    'relative_resize' => ['scale' => 1],
                ],
            ],
            'app_backend_admin_user_avatar_thumbnail' => [
                'filters' => [
                    'thumbnail' => [
                        'size' => [50, 50],
                        'mode' => 'outbound',
                    ],
                ],
            ],
        ],
    ]);

    $containerConfigurator->extension('sylius_mailer', [
        'sender' => [
            'name' => '%email_name%',
            'address' => '%email_sender%',
        ],
        'emails' => [
        ],
    ]);

    $containerConfigurator->extension('sylius_user', [
        'resources' => [
            'admin' => [
                'user' => [
                    'classes' => [
                        'model' => AdminUser::class,
                        'repository' => SyliusUserRepository::class,
                        'form' => AdminUserType::class,
                    ],
                ],
            ],
            'admin_oauth' => [
                'user' => [
                    'classes' => [
                        'model' => UserOAuth::class,
                        'interface' => UserOAuthInterface::class,
                        'controller' => ResourceController::class,
                    ],
                ],
            ],
        ],
    ]);

    $containerConfigurator->extension('vich_uploader', [
        'mappings' => [
            'admin_avatar' => [
                'uri_prefix' => '/media/avatar',
                'upload_destination' => '%kernel.project_dir%/public/media/avatar',
                'namer' => OrignameNamer::class,
            ],
        ],
    ]);

    if ('test' === $containerConfigurator->env()) {
        $containerConfigurator->extension('framework', [
            'cache' => [
                'pools' => [
                    'test.mailer_pool' => [
                        'adapter' => 'cache.adapter.filesystem',
                    ],
                ],
            ],
        ]);
    }
};

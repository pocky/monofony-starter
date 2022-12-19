<?php

declare(strict_types=1);

use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('secret', '%env(resolve:APP_SECRET)%');

    $containerConfigurator->extension('security', [
        'enable_authenticator_manager' => true,
        'providers' => [
            'sylius_admin_user_provider' => [
                'id' => 'sylius.admin_user_provider.email_or_name_based',
            ],
        ],
        'password_hashers' => [
           UserInterface::class => 'auto',
        ],
        'role_hierarchy' => [
            'ROLE_ADMIN' => 'ROLE_USER',
        ],
        'firewalls' => [
            'admin' => [
                'context' => 'admin',
                'pattern' => '/admin(?:/.*)?$',
                'provider' => 'sylius_admin_user_provider',
                'form_login' => [
                    'provider' => 'sylius_admin_user_provider',
                    'login_path' => 'sylius_backend_login',
                    'check_path' => 'sylius_backend_login_check',
                    'failure_path' => 'sylius_backend_login',
                    'default_target_path' => 'app_backend_dashboard',
                    'use_forward' => false,
                    'use_referer' => false,
                ],
                'remember_me' => [
                    'secret' => '%secret%',
                    'path' => '/admin',
                    'name' => 'APP_ADMIN_REMEMBER_ME',
                    'lifetime' => 31536000,
                    'remember_me_parameter' => '_remember_me',
                ],
                'logout' => [
                    'path' => 'sylius_backend_logout',
                    'target' => 'sylius_backend_login',
                ],
            ],
            'api_login' => [
                'pattern' => '^/api/authentication_token',
                'provider' => 'sylius_admin_user_provider',
                'stateless' => true,
                'json_login' => [
                    'check_path' => '/api/authentication_token',
                    'success_handler' => 'lexik_jwt_authentication.handler.authentication_success',
                    'failure_handler' => 'lexik_jwt_authentication.handler.authentication_failure',
                ],
            ],
            'api' => [
                'pattern' => '^/api',
                'provider' => 'sylius_admin_user_provider',
                'stateless' => true,
                'entry_point' => 'jwt',
                'jwt' => [],
                'refresh_jwt' => [
                    'check_path' => 'gesdinet_jwt_refresh_token',
                ],
            ],
            'dev' => [
                'pattern' => '^/(_(profiler|wdt)|css|images|js)/',
                'security' => false,
            ],
        ],
        'access_control' => [
            ['path' => '^/api/(authentication_token|token/refresh)', 'roles' => 'PUBLIC_ACCESS'],
            ['path' => '^/admin/login', 'role' => 'PUBLIC_ACCESS'],
            ['path' => '^/admin/login-check', 'role' => 'PUBLIC_ACCESS'],
            ['path' => '^/admin/dashboard', 'role' => 'ROLE_ADMIN'],
            ['path' => '^/admin.*', 'role' => 'ROLE_ADMIN'],
        ],
    ]);
};

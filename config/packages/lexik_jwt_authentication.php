<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('lexik_jwt_authentication', [
        'secret_key' => '%env(resolve:JWT_SECRET_KEY)%',
        'public_key' => '%env(resolve:JWT_PUBLIC_KEY)%',
        'pass_phrase' => '%env(JWT_PASSPHRASE)%',
    ]);

    if ('test' === $containerConfigurator->env()) {
        $containerConfigurator->extension('lexik_jwt_authentication', [
            'pass_phrase' => 'ALL_THAT_IS_GOLD_DOES_NOT_GLITTER_NOT_ALL_THOSE_WHO_WANDER_ARE_LOST',
            'token_ttl' => 315360000,
        ]);
    }
};

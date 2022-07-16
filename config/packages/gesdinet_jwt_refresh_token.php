<?php

declare(strict_types=1);

use App\Shared\Infrastructure\Persistence\Doctrine\ORM\Entity\RefreshToken;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('gesdinet_jwt_refresh_token', [
        'refresh_token_class' => RefreshToken::class,
    ]);
};

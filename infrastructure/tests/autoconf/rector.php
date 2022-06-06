<?php

declare(strict_types=1);

use Rector\Core\ValueObject\PhpVersion;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

use Rector\Config\RectorConfig;
use Rector\Symfony\Set\SymfonySetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->parallel();

    $rectorConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    $rectorConfig->phpVersion(PhpVersion::PHP_81);

    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_80,
        LevelSetList::UP_TO_PHP_81,
        SetList::CODE_QUALITY,
        SymfonySetList::SYMFONY_54,
        SetList::PHP_80,
        SetList::PHP_81
    ]);
};

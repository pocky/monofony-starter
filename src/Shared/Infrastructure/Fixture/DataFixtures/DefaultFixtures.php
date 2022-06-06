<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Fixture\DataFixtures;

use App\Shared\Infrastructure\Fixture\Story\DefaultAdministratorsStory;
use App\Shared\Infrastructure\Fixture\Story\DefaultAppUsersStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class DefaultFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        DefaultAdministratorsStory::load();
        DefaultAppUsersStory::load();
    }

    public static function getGroups(): array
    {
        return ['default'];
    }
}

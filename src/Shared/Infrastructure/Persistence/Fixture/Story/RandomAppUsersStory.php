<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Fixture\Story;

use App\Shared\Infrastructure\Persistence\Fixture\Factory\AppUserFactory;
use Zenstruck\Foundry\Story;

final class RandomAppUsersStory extends Story
{
    public function build(): void
    {
        AppUserFactory::createMany(10);
    }
}

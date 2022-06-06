<?php

declare(strict_types=1);

namespace App\Security\Shared\Infrastructure\Fixture\Story;

use App\Shared\Infrastructure\Fixture\Factory\AppUserFactory;
use Zenstruck\Foundry\Story;

final class TestAppUsersStory extends Story
{
    public function build(): void
    {
        AppUserFactory::createOne([
            'email' => 'api@sylius.com',
            'username' => 'sylius',
            'password' => 'sylius',
            'first_name' => 'Sam',
            'last_name' => 'Identifie',
        ]);

        AppUserFactory::createOne([
            'email' => 'another-customer@example.com',
            'username' => 'monofony',
            'password' => 'monofony',
            'first_name' => 'Another',
            'last_name' => 'Customer',
        ]);
    }
}

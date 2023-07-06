<?php

declare(strict_types=1);

namespace App\Security\Infrastructure\Persistence\Fixture\Story;

use App\Security\Infrastructure\Persistence\Fixture\Factory\AdminUserFactory;
use Zenstruck\Foundry\Story;

final class DefaultAdministratorsStory extends Story
{
    public function build(): void
    {
        AdminUserFactory::createOne([
            'email' => 'admin@example.com',
            'password' => 'admin',
        ]);
    }
}

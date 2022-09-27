<?php

declare(strict_types=1);

namespace App\Tests\Behat\Context\Setup;

use App\Security\Shared\Infrastructure\Persistence\Fixture\Factory\AdminUserFactory;
use Behat\Behat\Context\Context;
use Monofony\Bridge\Behat\Service\SharedStorageInterface;

final class AdminUserContext implements Context
{
    public function __construct(
        private readonly SharedStorageInterface $sharedStorage,
        private readonly AdminUserFactory $adminUserFactory,
    ) {
    }

    /**
     * @Given there is an administrator :email identified by :password
     * @Given /^there is(?:| also) an administrator "([^"]+)"$/
     */
    public function thereIsAnAdministratorIdentifiedBy($email, $password = 'admin'): void
    {
        $adminUser = $this->adminUserFactory::createOne(['email' => $email, 'password' => $password, 'enabled' => true])->object();
        $this->sharedStorage->set('administrator', $adminUser);
    }

    /**
     * @Given there is an administrator with name :username
     */
    public function thereIsAnAdministratorWithName($username): void
    {
        $adminUser = $this->adminUserFactory::createOne(['username' => $username])->object();

        $this->sharedStorage->set('administrator', $adminUser);
    }
}

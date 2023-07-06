<?php

declare(strict_types=1);

namespace App\Tests\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Monofony\Bridge\Behat\Service\SharedStorageInterface;
use Monofony\Contracts\Core\Model\User\AdminUserInterface;
use Zenstruck\Foundry\Proxy;

final readonly class AdminUserContext implements Context
{
    public function __construct(private SharedStorageInterface $sharedStorage)
    {
    }

    /**
     * @Transform /^(I|my)$/
     */
    public function getLoggedAdminUser(): AdminUserInterface|Proxy
    {
        return $this->sharedStorage->get('administrator');
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Monofony\Bridge\Behat\Service\SharedStorageInterface;

class UserContext implements Context
{
    public function __construct(private readonly SharedStorageInterface $sharedStorage)
    {
    }

    /**
     * @Transform /^(I|my|he|his|she|her|"this user")$/
     */
    public function getLoggedUser()
    {
        return $this->sharedStorage->get('user');
    }
}

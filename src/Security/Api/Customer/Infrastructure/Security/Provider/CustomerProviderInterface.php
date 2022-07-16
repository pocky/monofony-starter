<?php

declare(strict_types=1);

namespace App\Security\Api\Customer\Infrastructure\Security\Provider;

use Monofony\Contracts\Core\Model\Customer\CustomerInterface;

interface CustomerProviderInterface
{
    public function provide(string $email): CustomerInterface;
}

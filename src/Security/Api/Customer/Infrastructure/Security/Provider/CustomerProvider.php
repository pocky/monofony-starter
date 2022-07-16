<?php

declare(strict_types=1);

namespace App\Security\Api\Customer\Infrastructure\Security\Provider;

use Monofony\Contracts\Core\Model\Customer\CustomerInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;

final class CustomerProvider implements CustomerProviderInterface
{
    public function __construct(
        private readonly CanonicalizerInterface $canonicalizer,
        private readonly FactoryInterface $customerFactory,
        private readonly RepositoryInterface $customerRepository,
    ) {
    }

    public function provide(string $email): CustomerInterface
    {
        $emailCanonical = $this->canonicalizer->canonicalize($email);

        $customer = $this->customerRepository->findOneBy(['emailCanonical' => $emailCanonical]);

        if (!$customer instanceof \Monofony\Contracts\Core\Model\Customer\CustomerInterface) {
            /** @var CustomerInterface $customer */
            $customer = $this->customerFactory->createNew();
            $customer->setEmail($email);
        }

        return $customer;
    }
}

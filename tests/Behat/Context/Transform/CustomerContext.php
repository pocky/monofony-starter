<?php

declare(strict_types=1);

namespace App\Tests\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Monofony\Bridge\Behat\Service\SharedStorageInterface;
use Sylius\Component\Customer\Model\CustomerInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Zenstruck\Foundry\Proxy;

final class CustomerContext implements Context
{
    public function __construct(private readonly RepositoryInterface $customerRepository, private readonly FactoryInterface $customerFactory, private readonly SharedStorageInterface $sharedStorage)
    {
    }

    /**
     * @Transform :customer
     * @Transform /^customer "([^"]+)"$/
     */
    public function getOrCreateCustomerByEmail($email): object
    {
        $customer = $this->customerRepository->findOneBy(['email' => $email]);
        if (!$customer instanceof \Sylius\Component\Customer\Model\CustomerInterface) {
            /** @var CustomerInterface $customer */
            $customer = $this->customerFactory->createNew();
            $customer->setEmail($email);

            $this->customerRepository->add($customer);
        }

        return $customer;
    }

    /**
     * @Transform /^(he|his|she|her|their|the customer of my account)$/
     */
    public function getLastCustomer(): CustomerInterface|Proxy
    {
        return $this->sharedStorage->get('customer');
    }
}

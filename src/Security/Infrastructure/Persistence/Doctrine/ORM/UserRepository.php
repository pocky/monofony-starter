<?php

declare(strict_types=1);

namespace App\Security\Infrastructure\Persistence\Doctrine\ORM;

use Sylius\Bundle\UserBundle\Doctrine\ORM\UserRepository as BaseUserRepository;
use Sylius\Component\User\Model\UserInterface;

class UserRepository extends BaseUserRepository
{
    public function findOneByEmail(string $email): ?UserInterface
    {
        return $this->createQueryBuilder('o')
            ->andWhere('user.emailCanonical = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}

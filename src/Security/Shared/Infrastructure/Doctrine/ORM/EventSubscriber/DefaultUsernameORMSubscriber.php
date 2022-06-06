<?php

declare(strict_types=1);

namespace App\Security\Shared\Infrastructure\Doctrine\ORM\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\UnitOfWork;
use Monofony\Contracts\Core\Model\Customer\CustomerInterface;

/**
 * Keeps user's username synchronized with email.
 */
final class DefaultUsernameORMSubscriber implements EventSubscriber
{
    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [Events::onFlush, ];
    }

    public function onFlush(OnFlushEventArgs $onFlushEventArgs): void
    {
        $entityManager = $onFlushEventArgs->getEntityManager();
        $unitOfWork = $entityManager->getUnitOfWork();

        $this->processEntities($unitOfWork->getScheduledEntityInsertions(), $entityManager, $unitOfWork);
        $this->processEntities($unitOfWork->getScheduledEntityUpdates(), $entityManager, $unitOfWork);
    }

    private function processEntities(
        array $entities,
        EntityManagerInterface $entityManager,
        UnitOfWork $unitOfWork,
    ): void {
        foreach ($entities as $customer) {
            if (!$customer instanceof CustomerInterface) {
                continue;
            }

            $user = $customer->getUser();

            if (!$user instanceof \Sylius\Component\User\Model\UserInterface) {
                continue;
            }

            if ($customer->getEmail() === $user->getUsername() && $customer->getEmailCanonical() === $user->getUsernameCanonical()) {
                continue;
            }

            $user->setUsername($customer->getEmail());
            $user->setUsernameCanonical($customer->getEmailCanonical());

            $userMetadata = $entityManager->getClassMetadata($user::class);
            $unitOfWork->recomputeSingleEntityChangeSet($userMetadata, $user);
        }
    }
}

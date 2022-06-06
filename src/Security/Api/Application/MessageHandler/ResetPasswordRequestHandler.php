<?php

declare(strict_types=1);

namespace App\Security\Api\Application\MessageHandler;

use App\Security\Api\Application\Message\ResetPasswordRequest;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\UserBundle\UserEvents;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class ResetPasswordRequestHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly RepositoryInterface $customerRepository,
        private readonly GeneratorInterface $generator,
        private readonly EntityManagerInterface $entityManager,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(ResetPasswordRequest $message): void
    {
        $customer = $this->customerRepository->findOneBy(['emailCanonical' => $message->email]);

        if (!$customer instanceof \Monofony\Contracts\Core\Model\Customer\CustomerInterface) {
            return;
        }

        if (null === $user = $customer->getUser()) {
            return;
        }

        $user->setPasswordResetToken($this->generator->generate());
        $user->setPasswordRequestedAt(new \DateTime());

        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(new GenericEvent($user), UserEvents::REQUEST_RESET_PASSWORD_TOKEN);
    }
}

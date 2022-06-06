<?php

declare(strict_types=1);

namespace App\Security\Api\Application\MessageHandler;

use App\Security\Api\Application\Message\ChangeAppUserPassword;
use Doctrine\ORM\EntityManagerInterface;
use Monofony\Contracts\Core\Model\User\AppUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\PasswordUpdaterInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

final class ChangeAppUserPasswordHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly PasswordUpdaterInterface $passwordUpdater,
        private readonly UserRepositoryInterface $appUserRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(ChangeAppUserPassword $message): void
    {
        /** @var AppUserInterface|null $user */
        $user = $this->appUserRepository->find($message->getAppUserId());

        Assert::notNull($user);

        $user->setPlainPassword($message->newPassword);

        $this->passwordUpdater->updatePassword($user);

        $this->entityManager->flush();
    }
}

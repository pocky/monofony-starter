<?php

declare(strict_types=1);

namespace App\Security\Api\Application\MessageHandler;

use App\Security\Api\Application\Message\ResetPassword;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

final class ResetPasswordHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly RepositoryInterface $appUserRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestStack $requestStack,
        private readonly array $syliusResources,
    ) {
    }

    public function __invoke(ResetPassword $message): void
    {
        $user = $this->appUserRepository->findOneBy(['passwordResetToken' => $this->getToken()]);

        if (!$user instanceof \Sylius\Component\User\Model\UserInterface) {
            throw new NotFoundHttpException('Token not found.');
        }

        $lifetime = new \DateInterval($this->getTtl());
        if (!$user->isPasswordRequestNonExpired($lifetime)) {
            $this->handleExpiredToken($user);

            return;
        }

        $user->setPlainPassword($message->password);
        $user->setPasswordResetToken(null);
        $user->setPasswordRequestedAt(null);

        $this->entityManager->flush();
    }

    private function getToken(): string
    {
        /** @var Request $request */
        $request = $this->requestStack->getCurrentRequest();
        Assert::notNull($request);

        /** @var string $token */
        $token = $request->attributes->get('token');
        Assert::notNull($token);

        return $token;
    }

    private function getTtl(): string
    {
        /** @var string $ttl */
        $ttl = $this->syliusResources['sylius.app_user']['resetting']['token']['ttl'] ?? null;
        Assert::notNull($ttl, 'Token ttl was not found but it should.');

        return $ttl;
    }

    protected function handleExpiredToken(UserInterface $user): never
    {
        $user->setPasswordResetToken(null);
        $user->setPasswordRequestedAt(null);

        $this->entityManager->flush();

        throw new BadRequestHttpException('Token expired.');
    }
}

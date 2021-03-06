<?php

declare(strict_types=1);

namespace App\Security\Api\Customer\Infrastructure\Identifier;

use Monofony\Contracts\Api\Identifier\AppUserIdentifierNormalizerInterface;
use Monofony\Contracts\Core\Model\User\AppUserInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Security;

final class AppUserIdentifierNormalizer implements AppUserIdentifierNormalizerInterface
{
    public function __construct(
        private readonly Security $security,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $type, $format = null, array $context = []): string
    {
        $user = $this->security->getUser();

        if (!$user instanceof \Symfony\Component\Security\Core\User\UserInterface || !$user instanceof AppUserInterface) {
            throw new AccessDeniedHttpException();
        }

        return (string) $user->getCustomer()->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return 'me' === $data;
    }
}

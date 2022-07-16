<?php

declare(strict_types=1);

namespace App\Security\Api\Customer\Infrastructure\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Security\Api\Customer\Application\Message\AppUserIdAwareInterface;
use Monofony\Contracts\Core\Model\User\AppUserInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Security\Core\Security;

final class LoggedInAppUserIdAwareDataTransformer implements DataTransformerInterface
{
    public function __construct(
        private readonly Security $security,
    ) {
    }

    /**
     * @param AppUserIdAwareInterface $object
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function transform($object, string $to, array $context = []): AppUserIdAwareInterface
    {
        /** @var AppUserInterface|UserInterface $user */
        $user = $this->security->getUser();

        if (!$user instanceof AppUserInterface) {
            return $object;
        }

        $object->setAppUserId($user->getId());

        return $object;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return is_a($context['input']['class'], AppUserIdAwareInterface::class, true);
    }
}

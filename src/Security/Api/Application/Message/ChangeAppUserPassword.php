<?php

declare(strict_types=1);

namespace App\Security\Api\Application\Message;

use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ChangeAppUserPassword implements AppUserIdAwareInterface
{
    public ?int $appUserId = null;

    public function __construct(
        /**
         * @SecurityAssert\UserPassword(message="sylius.user.plainPassword.wrong_current")
         */
        #[NotBlank] #[Groups(groups: ['customer:password:write'])] public ?string $currentPassword = null,
        #[NotBlank] #[Groups(groups: ['customer:password:write'])] public ?string $newPassword = null,
    ) {
    }

    public function getAppUserId(): ?int
    {
        return $this->appUserId;
    }

    public function setAppUserId(?int $appUserId): void
    {
        $this->appUserId = $appUserId;
    }
}

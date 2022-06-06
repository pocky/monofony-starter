<?php

declare(strict_types=1);

namespace App\Security\Api\Application\Message;

interface AppUserIdAwareInterface
{
    public function getAppUserId(): ?int;

    public function setAppUserId(?int $appUserId): void;
}

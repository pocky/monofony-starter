<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Notifier;

interface BrowserNotificationInterface
{
    public function asBrowserNotification(): BrowserContext;
}

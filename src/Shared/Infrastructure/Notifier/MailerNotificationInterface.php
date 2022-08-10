<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Notifier;

interface MailerNotificationInterface
{
    public function asMailerNotification(): MailerContext;
}

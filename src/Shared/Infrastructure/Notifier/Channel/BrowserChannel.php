<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Notifier\Channel;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Notifier\Channel\ChannelInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\RecipientInterface;

final class BrowserChannel implements ChannelInterface
{
    public function __construct(
        private readonly RequestStack $requestStack,
    ) {
    }

    public function notify(Notification $notification, RecipientInterface $recipient, string $transportName = null): void
    {
        if (null === $request = $this->requestStack->getCurrentRequest()) {
            return;
        }

        $message = $notification->getSubject();
        if ('' !== $notification->getEmoji() && '0' !== $notification->getEmoji()) {
            $message = \Safe\sprintf('%s %s', $notification->getEmoji(), $message);
        }

        $request->getSession()->getFlashBag()->add($notification->getImportance(), $message);
    }

    public function supports(Notification $notification, RecipientInterface $recipient): bool
    {
        return true;
    }
}

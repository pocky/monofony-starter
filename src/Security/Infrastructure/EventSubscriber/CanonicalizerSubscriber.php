<?php

declare(strict_types=1);

namespace App\Security\Infrastructure\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;
use Sylius\Component\User\Model\UserInterface;

final class CanonicalizerSubscriber implements EventSubscriber
{
    public function __construct(private readonly CanonicalizerInterface $canonicalizer)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [Events::prePersist, Events::preUpdate];
    }

    public function canonicalize(LifecycleEventArgs $event): void
    {
        $item = $event->getEntity();

        if ($item instanceof UserInterface) {
            $item->setUsernameCanonical($this->canonicalizer->canonicalize($item->getUsername()));
            $item->setEmailCanonical($this->canonicalizer->canonicalize($item->getEmail()));
        }
    }

    public function prePersist(LifecycleEventArgs $event): void
    {
        $this->canonicalize($event);
    }

    public function preUpdate(LifecycleEventArgs $event): void
    {
        $this->canonicalize($event);
    }
}

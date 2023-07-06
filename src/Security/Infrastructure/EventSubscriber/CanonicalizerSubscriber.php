<?php

declare(strict_types=1);

namespace App\Security\Infrastructure\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;
use Sylius\Component\User\Model\UserInterface;

final readonly class CanonicalizerSubscriber implements EventSubscriber
{
    public function __construct(private CanonicalizerInterface $canonicalizer)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function canonicalize(LifecycleEventArgs $event): void
    {
        $item = $event->getObject();

        if ($item instanceof UserInterface && method_exists($item, 'getUsername')) {
            $item->setUsernameCanonical($this->canonicalizer->canonicalize($item->getUsername()));
            $item->setEmailCanonical($this->canonicalizer->canonicalize($item->getEmail()));
        }
    }

    public function prePersist(PrePersistEventArgs $event): void
    {
        $this->canonicalize($event);
    }

    public function preUpdate(PreUpdateEventArgs $event): void
    {
        $this->canonicalize($event);
    }
}

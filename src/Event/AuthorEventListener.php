<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\Author;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class AuthorEventListener
{
    public function __construct(private readonly FlashBagInterface $flashBag)
    {
    }

    // the entity listener methods receive two arguments:
    // the entity instance and the lifecycle event
    public function postUpdate(Author $author, LifecycleEventArgs $event): void
    {
        // ... do something to notify the changes
        $this->flashBag->set('lifecycleevent.message', self::class.'::postUpdate(...) has been fired.');
    }
}

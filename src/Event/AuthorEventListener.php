<?php

namespace App\Event;

use App\Entity\Author;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class AuthorEventListener
{
    private $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    // the entity listener methods receive two arguments:
    // the entity instance and the lifecycle event
    public function postUpdate(Author $author, LifecycleEventArgs $event): void
    {
        // ... do something to notify the changes
        $this->flashBag->set('lifecycleevent.message', self::class.'::postUpdate(...) has been fired.');
    }
}
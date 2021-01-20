<?php

namespace App\EventListener;

use App\Entity\Path;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * Class EntityPathFlushCache
 *
 * This event will flush the entity path cache every time Doctrine update the table.
 * We are listing to the doctrine.orm.entity_listener as defined in the services.yaml file.
 */
class EntityPathFlushCache
{
    private AdapterInterface $entityPathCache;

    public function __construct(
        AdapterInterface $entityPathCache
    ) {
        $this->entityPathCache = $entityPathCache;
    }

    public function postPersist(Path $path, LifecycleEventArgs $event) : void
    {
        $this->entityPathCache->clear();
    }
}

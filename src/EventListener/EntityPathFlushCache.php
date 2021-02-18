<?php

namespace App\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Class EntityPathFlushCache
 *
 * This event will flush the entity path cache every time Doctrine update the table.
 * We are listing to the doctrine.orm.entity_listener as defined in the services.yaml file.
 */
class EntityPathFlushCache
{
    private CacheItemPoolInterface $entityPathCache;

    public function __construct(
        CacheItemPoolInterface $entityPathCache
    ) {
        $this->entityPathCache = $entityPathCache;
    }

    /**
     * @param $entity, don't rely on this parameter since its type is undefined. It will in fact depends of the entity
     *                 that triggers the event.
     * @param LifecycleEventArgs $event
     */
    public function execute($entity, LifecycleEventArgs $event) : void
    {
        $this->entityPathCache->clear();
    }
}

<?php

namespace App\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use Psr\Cache\CacheItemPoolInterface;

class EntityArticleFlushCache
{
    private CacheItemPoolInterface $entityArticleCache;

    public function __construct(
        CacheItemPoolInterface $entityArticleCache
    ) {
        $this->entityArticleCache = $entityArticleCache;
    }

    /**
     * @param $entity, don't rely on this parameter since its type is undefined. It will in fact depends of the entity
     *                 that triggers the event.
     * @param LifecycleEventArgs $event
     */
    public function execute($entity, LifecycleEventArgs $event) : void
    {
        $this->entityArticleCache->clear();
    }
}

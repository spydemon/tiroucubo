<?php

namespace App\EventListener;

use App\Entity\Article;
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

    public function postPersist(Article $article, LifecycleEventArgs $event) : void
    {
        $this->entityArticleCache->clear();
    }
}

<?php

namespace App\Repository;

use App\Entity\Path;
use App\Manager\Cache\NameManager;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * @method Path|null find($id, $lockMode = null, $lockVersion = null)
 * @method Path|null findOneBy(array $criteria, array $orderBy = null)
 * @method Path[]    findAll()
 * @method Path[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PathRepository extends ServiceEntityRepository
{
    private CacheInterface $entityPathCache;
    private NameManager $nameManager;

    public function __construct(
        CacheInterface $entityPathCache,
        ManagerRegistry $registry,
        NameManager $nameManager
    ) {
        $this->entityPathCache = $entityPathCache;
        $this->nameManager = $nameManager;
        parent::__construct($registry, Path::class);
    }

    public function findByPath(string $path) : ?Path
    {
        $pathId = $this->entityPathCache->get(
            $this->nameManager->encodeCacheKey('find_by_path_' . $path),
            function (ItemInterface $item) use ($path) {
                return $this->findIdByPathWithoutCache($path);
            }
        );
        return $this->find($pathId);
    }

    public function findBySlugAndParent(string $slug, ?Path $parent) : Path
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->andWhere('c.slug = :slug')
            ->setParameter('slug', $slug);
        if ($parent) {
            $queryBuilder->andWhere('c.parent = :parent')
                ->setParameter('parent', $parent);
        } else {
            $queryBuilder->andWhere('c.parent IS NULL');
        }
        return $queryBuilder->getQuery()->setCacheable(true)->getSingleResult();
    }

    private function findIdByPathWithoutCache(string $pathString) : ?int
    {
        $pathString = explode('/', $pathString);
        $path = null;
        while ($nextSlug = array_shift($pathString)) {
            $path = $this->findBySlugAndParent($nextSlug, $path);
        }
        return $path->getId();
    }
}

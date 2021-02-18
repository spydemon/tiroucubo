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
    private ArticleVersionRepository $articleVersionRepository;
    private CacheInterface $entityPathCache;
    private NameManager $nameManager;

    public function __construct(
        ArticleVersionRepository $articleVersionRepository,
        CacheInterface $entityPathCache,
        ManagerRegistry $registry,
        NameManager $nameManager
    ) {
        $this->articleVersionRepository = $articleVersionRepository;
        $this->entityPathCache = $entityPathCache;
        $this->nameManager = $nameManager;
        parent::__construct($registry, Path::class);
    }

    public function countActiveContent(Path $path, bool $recursive = true) : int
    {
        return $this->entityPathCache->get(
            $this->nameManager->encodeCacheKey('count_active_content_' . $path),
            function (ItemInterface $item) use ($path, $recursive) {
                return $this->countActiveContentWithoutCache($path, $recursive);
            }
        );
    }

    public function findByPath(string $path) : ?Path
    {
        // We are caching only the entity ID because the serialization of Doctrine items that contains Collections seems broken.
        $pathId = $this->entityPathCache->get(
            $this->nameManager->encodeCacheKey('find_by_path_' . $path),
            function (ItemInterface $item) use ($path) {
                return $this->findIdByPathWithoutCache($path);
            }
        );
        return $pathId ? $this->find($pathId) : null;
    }

    public function findBySlugAndParent(string $slug, ?Path $parent) : ?Path
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
        return $queryBuilder->getQuery()->setCacheable(true)->getOneOrNullResult();
    }

    private function countActiveContentWithoutCache(Path $path, bool $recursive = true) : int
    {
        $count = 0;
        foreach ($path->getArticles() as $currentArticle) {
            if ($this->articleVersionRepository->findActiveVersionForArticle($currentArticle)) {
                $count++;
            }
        }
        if (!$recursive) {
            return $count;
        }
        foreach ($path->getChild() as $currentChild) {
            $count += $this->countActiveContent($currentChild);
        }
        return $count;
    }

    private function findIdByPathWithoutCache(string $pathString) : ?int
    {
        $pathString = explode('/', $pathString);
        $path = null;
        while ($nextSlug = array_shift($pathString)) {
            $path = $this->findBySlugAndParent($nextSlug, $path);
            if (is_null($path)) {
                return null;
            }
        }
        return $path->getId();
    }
}

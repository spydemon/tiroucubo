<?php

namespace App\Repository;

use App\Entity\Path;
use App\Manager\Cache\NameManager;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        ArticleVersionRepository $articleVersionRepository,
        CacheInterface $entityPathCache,
        ManagerRegistry $registry,
        NameManager $nameManager,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->articleVersionRepository = $articleVersionRepository;
        $this->entityPathCache = $entityPathCache;
        $this->nameManager = $nameManager;
        $this->urlGenerator = $urlGenerator;
        parent::__construct($registry, Path::class);
    }

    public function findActiveArticlesRecursivelyForPath(Path $path) : array
    {
        return $this->entityPathCache->get(
            $this->nameManager->encodeCacheKey('find_active_article_recursively_for_path_' . $path),
            function (ItemInterface $item) use ($path) {
                return $this->findActiveArticlesRecursivelyForPathWithoutCache($path);
            }
        );
    }

    public function findAllChildRecursively(Path $path) : array
    {
        return $this->entityPathCache->get(
            $this->nameManager->encodeCacheKey('find_all_child_recursively_' . $path),
            function (ItemInterface $item) use ($path) {
                return $this->findArticleRecursivelyForPathWithoutCache($path);
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

    public function getUrlForPath(Path $path) : string
    {
        $root = $this->urlGenerator->generate('default', [], UrlGeneratorInterface::ABSOLUTE_URL);
        return $root . $path;
    }

    private function findActiveArticlesRecursivelyForPathWithoutCache(Path $path) : array
    {
        $candidatesPath = $this->findAllChildRecursively($path);
        $result = [];
        $collection = $this
            ->getEntityManager()
            ->createQuery(<<<DQL
                SELECT a FROM App\Entity\Article a
                WHERE a.path IN (:path)
                ORDER BY a.creation_date DESC
            DQL)
            ->setParameter('path', $candidatesPath)
            ->setCacheable(true)
            ->getResult();
        foreach ($collection as $currentArticle) {
            if ($this->articleVersionRepository->findActiveVersionForArticle($currentArticle)) {
                $result[] = $currentArticle;
            }
        }
        return $result;
    }

    private function findArticleRecursivelyForPathWithoutCache(Path $path) : array
    {
        $collection = [];
        $collection[] = $path;
        foreach ($path->getChild() as $child) {
            $collection = array_merge($collection, $this->findAllChildRecursively($child));
        }
        return $collection;
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

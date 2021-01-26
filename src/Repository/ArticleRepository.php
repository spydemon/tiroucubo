<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    private CacheInterface $entityArticleCache;

    public function __construct(
        CacheInterface $entityArticleCache,
        ManagerRegistry $registry
    ) {
        $this->entityArticleCache = $entityArticleCache;
        parent::__construct($registry, Article::class);
    }

    public function getAllArticlesSortedByCreationDate() : array
    {
        return $this->getEntityManager()
            ->createQuery('SELECT a FROM App\Entity\Article a ORDER BY a.creation_date DESC')
            ->setCacheable(true)
            ->getResult();
    }

    public function getAllArticlesSortedById() : array
    {
        return $this->getEntityManager()
            ->createQuery('SELECT a FROM App\Entity\Article a ORDER BY a.id DESC')
            ->setCacheable(true)
            ->getResult();
    }

    public function getAllArticlesSortedByPath() : array
    {
        return $this->entityArticleCache->get('get_all_article_sorted_by_path', function () {
            $unsorted = $this->findAll();
            $sorted = [];
            foreach ($unsorted as $currentArticle) {
                $sorted[$currentArticle->getPath() . '/' . $currentArticle->getId()] = $currentArticle;
            }
            ksort($sorted);
            return $sorted;
        });
    }

    public function getAllArticlesSortedByUpdateDate() : array
    {
        return $this->getEntityManager()
            ->createQuery('SELECT a FROM App\Entity\Article a ORDER BY a.update_date DESC')
            ->setCacheable(true)
            ->getResult();
    }
}

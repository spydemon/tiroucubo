<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\ArticleVersion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ArticleVersion|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArticleVersion|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArticleVersion[]    findAll()
 * @method ArticleVersion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleVersionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArticleVersion::class);
    }

    public function createNewVersionForArticle(Article $article): ArticleVersion
    {
        $version = new ArticleVersion();
        $version->setArticle($article);
        return $version;
    }

    public function findActiveVersionForArticle(Article $article): ?ArticleVersion
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.article = :article')
            ->andWhere('q.active = true')
            ->setParameter('article', $article)
            ->getQuery()
            ->setCacheable(true)
            ->getOneOrNullResult();
    }

    public function findLastVersionForArticle(Article $article): ?ArticleVersion
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.article = :article')
            ->orderBy('q.creation_date', 'DESC')
            ->setParameter('article', $article)
            ->setMaxResults(1)
            ->getQuery()
            ->setCacheable(true)
            ->getOneOrNullResult();
    }

    public function findVersionByArticleAndSlug(Article $article, string $slug): ?ArticleVersion
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.article = :article')
            ->andWhere('q.slug = :slug')
            ->setParameter('article', $article)
            ->setParameter('slug', $slug)
            ->getQuery()
            ->setCacheable(true)
            ->getOneOrNullResult();
    }
}

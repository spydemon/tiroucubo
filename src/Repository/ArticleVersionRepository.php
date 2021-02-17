<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\ArticleVersion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method ArticleVersion|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArticleVersion|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArticleVersion[]    findAll()
 * @method ArticleVersion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleVersionRepository extends ServiceEntityRepository
{
    private Connection $connection;

    public function __construct(Connection $connection, ManagerRegistry $registry)
    {
        $this->connection = $connection;
        parent::__construct($registry, ArticleVersion::class);
    }

    public function activeVersion(ArticleVersion $version) : void
    {
        try {
            $this->getEntityManager()->getConnection()->beginTransaction();
            // Only a single version can be activated for a given product. This SQL instruction will thus disable all
            // versions that exists for the product that owns the version to activate beforehand.
            $this->getEntityManager()->createQuery(
                'UPDATE App\Entity\ArticleVersion t SET t.active = 0 WHERE t.article = :article'
            )->setParameter('article', $version->getArticle())
                ->execute();
            $version->setActive(true);
            $this->getEntityManager()->persist($version);
            $this->getEntityManager()->flush();
            $this->getEntityManager()->getConnection()->commit();
        } catch (Exception $e) {
            $this->getEntityManager()->getConnection()->rollback();
            throw $e;
        }
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

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
}

<?php

namespace App\Repository;

use App\Entity\PathTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PathTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method PathTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method PathTranslation[]    findAll()
 * @method PathTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PathTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PathTranslation::class);
    }

    public function findByPathEn(string $path) : ?PathTranslation
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.path_en = :path')
            ->setParameter('path', $path)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByPathFr(string $path) : ?PathTranslation
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.path_fr = :path')
            ->setParameter('path', $path)
            ->getQuery()
            ->getOneOrNullResult();
    }
}

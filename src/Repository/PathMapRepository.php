<?php

namespace App\Repository;

use App\Entity\PathMap;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PathMap|null find($id, $lockMode = null, $lockVersion = null)
 * @method PathMap|null findOneBy(array $criteria, array $orderBy = null)
 * @method PathMap[]    findAll()
 * @method PathMap[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PathMapRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PathMap::class);
    }

    public function getPathMapForUrl(string $url) : ?PathMap
    {
        $result = $this->createQueryBuilder('p')
            ->andWhere(':url LIKE CONCAT(p.url, \'%\')')
            ->orderBy('p.priority', 'DESC')
            ->setParameter('url', $url)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
        return count($result) ? $result[0] : null;
    }
}

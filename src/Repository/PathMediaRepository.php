<?php

namespace App\Repository;

use App\Entity\Media;
use App\Entity\Path;
use App\Entity\PathMedia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PathMedia|null find($id, $lockMode = null, $lockVersion = null)
 * @method PathMedia|null findOneBy(array $criteria, array $orderBy = null)
 * @method PathMedia[]    findAll()
 * @method PathMedia[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PathMediaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PathMedia::class);
    }

    public function findMediaByPath(Path $path) : ?Media
    {
        $result = $this->createQueryBuilder('m')
            ->andWhere('m.path= :path')
            ->setParameter('path', $path)
            ->getQuery()
            ->setCacheable(true)
            ->getOneOrNullResult();
        return $result ? $result->getMedia() : null;
    }

    /**
     * @param Path $path
     * @return Media[]
     */
    public function findPathsByMedia(Media $media) : array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.media= :media')
            ->setParameter('media', $media)
            ->getQuery()
            ->setCacheable(true)
            ->getArrayResult();
    }
}

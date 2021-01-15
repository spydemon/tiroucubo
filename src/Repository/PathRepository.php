<?php

namespace App\Repository;

use App\Entity\Path;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Path|null find($id, $lockMode = null, $lockVersion = null)
 * @method Path|null findOneBy(array $criteria, array $orderBy = null)
 * @method Path[]    findAll()
 * @method Path[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PathRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Path::class);
    }

    public function findByPath(string $path) : ?Path
    {
        $path = explode('/', $path);
        $category = null;
        while ($nextSlug = array_shift($path)) {
            $category = $this->findBySlugAndParent($nextSlug, $category);
        }
        return $category;
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
        return $queryBuilder->getQuery()->getSingleResult();
    }
}

<?php

namespace App\Manager\Path;

use App\Entity\Path;
use App\Repository\PathMediaRepository;
use Doctrine\Persistence\ManagerRegistry;

class PathRemoverManager
{
    private ManagerRegistry $registry;
    private PathMediaRepository $pathMediaRepository;

    public function __construct(ManagerRegistry $registry, PathMediaRepository $pathMediaRepository)
    {
        $this->pathMediaRepository = $pathMediaRepository;
        $this->registry = $registry;
    }

    /**
     * This function will remove a given $path and its parents if they don't have child anymore.
     * This function will also remove PathMedia entities associated to the media.
     * Warning: the media will not be deleted if no path link to it anymore. This action should be done somewhere else.
     *
     * @param Path $path
     */
    public function removePath(Path $path)
    {
        $parent = $path->getParent();
        $mediaPaths = $this->pathMediaRepository->findPathMediaByPath($path);
        $this->registry->getConnection()->beginTransaction();
        foreach ($mediaPaths as $currentMediaPath) {
            $this->registry->getManager()->remove($currentMediaPath);
        }
        $this->registry->getManager()->remove($path);
        $this->registry->getManager()->flush();
        if ($parent->getChild()->count() == 1) {
            $this->removePath($parent);
        }
        $this->registry->getConnection()->commit();
    }
}
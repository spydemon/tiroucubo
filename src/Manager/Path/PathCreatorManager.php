<?php

namespace App\Manager\Path;

use App\Entity\Path;
use App\Repository\PathRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class PathCreatorManager
{
    private EntityManagerInterface $entityManager;
    private PathRepository $pathRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        PathRepository $pathRepository
    ) {
        $this->entityManager = $entityManager;
        $this->pathRepository = $pathRepository;
    }

    /**
     * Will create all missing Path objects for generating the path chain represented by $path, and will return the last one.
     * Example: if $path = 'fr/magento/installation/docker', we will check that the 'fr' path exists and created it if its not the case,
     * next we will check that a 'magento' child exists in it and we will create it otherwise, etc.
     */
    public function createFromString(string $path) : Path
    {
        try {
            $this->entityManager->getConnection()->beginTransaction();
            $path = $this->unstackPath($path);
            $this->entityManager->getConnection()->commit();
            return $path;
        } catch (Exception $e) {
            $this->entityManager->rollback();
            throw $e;
        }
    }

    private function unstackPath(string $remainingPath, ?Path $parent = null) : Path
    {
        static $pathSplitRegexp = <<<REGEXP
            ~
                (?<current_path>.*?)   # We fetch the begging of the remaining path to its end or to the first slash in the string.
                (?:/|$)
                (?<remaining_path>.*)  # We fetch everything after the first slash (it will be null if no slash was present in the initial string).
            ~x
            REGEXP;
        preg_match($pathSplitRegexp, $remainingPath, $matches);
        $current = $this->pathRepository->findBySlugAndParent($matches['current_path'], $parent);
        if (!$current) {
            $current = new Path();
            $current->setParent($parent);
            $current->setSlug($matches['current_path']);
            $current->setTitle($matches['current_path']);
            $this->entityManager->persist($current);
            $this->entityManager->flush();
        }
        if ($matches['remaining_path']) {
            return $this->unstackPath($matches['remaining_path'], $current);
        } else {
            return $current;
        }
    }
}

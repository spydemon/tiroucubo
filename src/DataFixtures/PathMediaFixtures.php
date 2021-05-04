<?php

namespace App\DataFixtures;

use App\Entity\PathMedia;
use App\Repository\MediaRepository;
use App\Repository\PathRepository;
use Doctrine\Bundle\FixturesBundle\Tests\Fixtures\FooBundle\DataFixtures\WithDependenciesFixtures;
use Doctrine\Persistence\ObjectManager;

class PathMediaFixtures extends WithDependenciesFixtures
{

    private MediaRepository $mediaRepository;
    private PathRepository $pathRepository;

    public function __construct(MediaRepository $mediaRepository, PathRepository $pathRepository)
    {
        $this->mediaRepository = $mediaRepository;
        $this->pathRepository = $pathRepository;
    }

    public function getDependencies(): array
    {
        return [
            MediaFixtures::class,
            PathFixtures::class
        ];
    }

    public function load(ObjectManager $objectManager) : void
    {
        $pathEn = $this->pathRepository->findByPath('en/pictures/mir-test.webp');
        $pathFr = $this->pathRepository->findByPath('fr/images/mir-test.webp');
        $media = $this->mediaRepository->find(1);
        foreach ([$pathEn, $pathFr] as $path) {
            $mediaPath = new PathMedia();
            $mediaPath->setMedia($media)->setPath($path);
            $objectManager->persist($mediaPath);
        }
        $objectManager->flush();
    }
}
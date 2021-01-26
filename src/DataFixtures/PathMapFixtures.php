<?php

namespace App\DataFixtures;

use App\Entity\PathMap;
use App\Repository\PathRepository;
use Doctrine\Bundle\FixturesBundle\Tests\Fixtures\FooBundle\DataFixtures\WithDependenciesFixtures;
use Doctrine\Persistence\ObjectManager;

class PathMapFixtures extends WithDependenciesFixtures
{
    private PathRepository $pathRepository;

    private array $data = [
        // This first entry was added in order to check that the priority field is correctly handled.
        [ 'root_url' => '/en/', 'root_path' => 'en/magento/installation', 'priority' => -1 ],
        [ 'root_url' => '/en/magento', 'root_path' => 'en/magento', 'priority' => 0 ],
        [ 'root_url' => '/fr/magento', 'root_path' => 'fr/magento', 'priority' => 0 ],
        [ 'root_url' => '/admin', 'root_path' => 'admin', 'priority' => 0 ],
    ];

    public function __construct(
        PathRepository $pathRepository
    ) {
        $this->pathRepository = $pathRepository;
    }

    public function getDependencies(): array
    {
        return [PathFixtures::class];
    }

    public function load(ObjectManager $manager) : void
    {
        foreach ($this->data as $currentData) {
            $pathMap = new PathMap();
            $path = $this->pathRepository->findByPath($currentData['root_path']);
            $pathMap->setPath($path);
            $pathMap->setUrl($currentData['root_url']);
            $pathMap->setPriority($currentData['priority']);
            $manager->persist($pathMap);
        }
        $manager->flush();
    }
}

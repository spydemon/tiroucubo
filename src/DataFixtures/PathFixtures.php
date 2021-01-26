<?php

namespace App\DataFixtures;

use App\Entity\Path;
use App\Repository\PathRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PathFixtures extends Fixture
{
    private PathRepository $pathRepository;

    private array $data = [
        ['slug' => 'en', 'parent_path' => '', 'title' => 'English tree'],
        ['slug' => 'magento', 'parent_path' => 'en', 'title' => 'Magento'],
        ['slug' => 'installation', 'parent_path' => 'en/magento', 'title' => 'Installation'],
        ['slug' => 'docker-configuration', 'parent_path' => 'en/magento/installation', 'title' => 'Docker configuration'],
        ['slug' => 'composer', 'parent_path' => 'en/magento/installation', 'title' => 'Composer'],
        ['slug' => 'configuration', 'parent_path' => 'en/magento/installation', 'title' => 'Configuration'],
        ['slug' => 'use-of-the-cms', 'parent_path' => 'en/magento', 'title' => 'Use of the CMS'],
        ['slug' => 'all-about-customers', 'parent_path' => 'en/magento/use-of-the-cms', 'title' => 'All about customers'],
        ['slug' => 'product-configuration', 'parent_path' => 'en/magento/use-of-the-cms', 'title' => 'Product configuration'],
        ['slug' => 'fr', 'parent_path' => '', 'title' => 'Arbre français'],
        ['slug' => 'magento', 'parent_path' => 'fr', 'title' => 'Magento'],
        ['slug' => 'installation', 'parent_path' => 'fr/magento', 'title' => 'Installation'],
        ['slug' => 'configuration-docker', 'parent_path' => 'fr/magento/installation', 'title' => 'Configuration Docker'],
        ['slug' => 'composer', 'parent_path' => 'fr/magento/installation', 'title' => 'Composer'],
        ['slug' => 'configuration', 'parent_path' => 'fr/magento/installation', 'title' => 'Configuration'],
        ['slug' => 'utilisation-du-cms', 'parent_path' => 'fr/magento', 'title' => 'Utilisation du CMS'],
        ['slug' => 'tout-a-propos-des-clients', 'parent_path' => 'fr/magento/utilisation-du-cms', 'title' => 'Tout à propos des clients'],
        ['slug' => 'configuration-des-produits', 'parent_path' => 'fr/magento/utilisation-du-cms', 'title' => 'Configuration des produits'],
        ['slug' => 'admin', 'parent_path' => '', 'title' => 'Admin'],
        ['slug' => 'dashboard', 'parent_path' => 'admin', 'title' => 'Dashboard'],
        ['slug' => 'article', 'parent_path' => 'admin', 'title' => 'Articles'],
    ];

    public function __construct(
        PathRepository $pathRepository
    ) {
        $this->pathRepository = $pathRepository;
    }

    public function load(ObjectManager $objectManager)
    {
        foreach ($this->data as $currentPathData) {
            $parent = null;
            if ($parentPath = $currentPathData['parent_path']) {
                $parent = $this->pathRepository->findByPath($parentPath);
            }
            $path = new Path();
            $path->setParent($parent);
            $path->setSlug($currentPathData['slug']);
            $path->setTitle($currentPathData['title']);
            $objectManager->persist($path);
            // The flush should be in the foreach for allowing children to fetch their parent.
            $objectManager->flush();
        }
    }
}

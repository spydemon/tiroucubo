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
        ['slug' => 'en', 'parent_path' => '', 'title' => 'English tree', 'custom_template' => 'front/path/home_en.html.twig'],
        ['slug' => 'magento', 'parent_path' => 'en', 'title' => 'Magento'],
        ['slug' => 'installation', 'parent_path' => 'en/magento', 'title' => 'Installation'],
        ['slug' => 'docker-configuration', 'parent_path' => 'en/magento/installation', 'title' => 'Docker configuration'],
        ['slug' => 'composer', 'parent_path' => 'en/magento/installation', 'title' => 'Composer'],
        ['slug' => 'configuration', 'parent_path' => 'en/magento/installation', 'title' => 'Configuration'],
        ['slug' => 'use-of-the-cms', 'parent_path' => 'en/magento', 'title' => 'Use of the CMS'],
        ['slug' => 'all-about-customers', 'parent_path' => 'en/magento/use-of-the-cms', 'title' => 'All about customers'],
        ['slug' => 'product-configuration', 'parent_path' => 'en/magento/use-of-the-cms', 'title' => 'Product configuration'],
        ['slug' => 'fr', 'parent_path' => '', 'title' => 'Arbre français', 'custom_template' => 'front/path/home_fr.html.twig'],
        ['slug' => 'magento', 'parent_path' => 'fr', 'title' => 'Magento'],
        ['slug' => 'installation', 'parent_path' => 'fr/magento', 'title' => 'Installation'],
        ['slug' => 'configuration-docker', 'parent_path' => 'fr/magento/installation', 'title' => 'Configuration Docker'],
        ['slug' => 'composer', 'parent_path' => 'fr/magento/installation', 'title' => 'Composer'],
        ['slug' => 'configuration', 'parent_path' => 'fr/magento/installation', 'title' => 'Configuration'],
        ['slug' => 'utilisation-du-cms', 'parent_path' => 'fr/magento', 'title' => 'Utilisation du CMS'],
        ['slug' => 'tout-a-propos-des-clients', 'parent_path' => 'fr/magento/utilisation-du-cms', 'title' => 'Tout à propos des clients'],
        ['slug' => 'configuration-des-produits', 'parent_path' => 'fr/magento/utilisation-du-cms', 'title' => 'Configuration des produits'],
        ['slug' => 'admin', 'parent_path' => '', 'title' => 'Admin', 'type' => Path::TYPE_ALWAYS_VISIBLE],
        ['slug' => 'dashboard', 'parent_path' => 'admin', 'title' => 'Dashboard', 'type' => Path::TYPE_ALWAYS_VISIBLE],
        ['slug' => 'article', 'parent_path' => 'admin', 'title' => 'Articles', 'type' => Path::TYPE_ALWAYS_VISIBLE],
        ['slug' => 'linux', 'parent_path' => 'fr', 'title' => 'Linux'],
        ['slug' => 'theorie', 'parent_path' => 'fr/linux', 'title' => 'Théorie'],
        ['slug' => 'histoire-de-la-creation', 'parent_path' => 'fr/linux/theorie', 'title' => 'L\'histoire de la création de Linux'],
        ['slug' => 'path', 'parent_path' => 'admin', 'title' => 'Paths', 'type' => Path::TYPE_ALWAYS_VISIBLE],
        ['slug' => 'media', 'parent_path' => 'admin', 'title' => 'Medias', 'type' => Path::TYPE_ALWAYS_VISIBLE],
        ['slug' => 'images', 'parent_path' => 'fr', 'title' => 'images', 'type' => Path::TYPE_MEDIA],
        ['slug' => 'mir-test.webp', 'parent_path' => 'fr/images', 'title' => 'images', 'type' => Path::TYPE_MEDIA],
        ['slug' => 'pictures', 'parent_path' => 'en', 'title' => 'pictures', 'type' => Path::TYPE_MEDIA],
        ['slug' => 'mir-test.webp', 'parent_path' => 'en/pictures', 'title' => 'picture', 'type' => Path::TYPE_MEDIA],
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
            if (isset($currentPathData['type'])) {
                $path->setType($currentPathData['type']);
            }
            if (isset($currentPathData['custom_template'])) {
                $path->setCustomTemplate($currentPathData['custom_template']);
            }
            $objectManager->persist($path);
            // The flush should be in the foreach for allowing children to fetch their parent.
            $objectManager->flush();
        }
    }
}

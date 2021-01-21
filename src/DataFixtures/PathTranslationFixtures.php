<?php

namespace App\DataFixtures;

use App\Entity\PathTranslation;
use Doctrine\Bundle\FixturesBundle\Tests\Fixtures\FooBundle\DataFixtures\WithDependenciesFixtures;
use Doctrine\Persistence\ObjectManager;

class PathTranslationFixtures extends WithDependenciesFixtures
{
    private array $data = [
        [ 'path_en' => '/en', 'path_fr' => '/fr'],
        [ 'path_en' => '/en/login', 'path_fr' => '/fr/login'],
        [ 'path_en' => '/en/magento', 'path_fr' => '/fr/magento'],
        [ 'path_en' => '/en/magento/installation', 'path_fr' => '/fr/magento/installation'],
        [ 'path_en' => '/en/magento/installation/docker-configuration', 'path_fr' => '/fr/magento/installation/configuration-docker'],
        [ 'path_en' => '/en/magento/installation/composer', 'path_fr' => '/fr/magento/installation/composer'],
        [ 'path_en' => '/en/magento/installation/configuration', 'path_fr' => '/fr/magento/installation/configuration'],
        [ 'path_en' => '/en/magento/use-of-the-cms', 'path_fr' => '/fr/magento/utilisation-du-cms'],
        [ 'path_en' => '/en/magento/use-of-the-cms/all-about-customers', 'path_fr' => '/fr/magento/utilisation-du-cms/tout-a-propos-des-clients'],
        [ 'path_en' => '/en/magento/use-of-the-cms/product-configuration', 'path_fr' => '/fr/magento/utilisation-du-cms/configuration-des-produits'],
    ];

    public function getDependencies(): array
    {
        return [PathFixtures::class];
    }

    public function load(ObjectManager $manager) : void
    {
        foreach ($this->data as $currentData) {
            $pathTranslation = new PathTranslation();
            $pathTranslation->setPathEn($currentData['path_en']);
            $pathTranslation->setPathFr($currentData['path_fr']);
            $manager->persist($pathTranslation);
        }
        $manager->flush();
    }
}

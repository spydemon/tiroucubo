<?php

namespace App\DataFixtures;

use App\Repository\ArticleRepository;
use App\Repository\ArticleVersionRepository;
use Doctrine\Bundle\FixturesBundle\Tests\Fixtures\FooBundle\DataFixtures\WithDependenciesFixtures;
use Doctrine\Persistence\ObjectManager;

class ArticleVersionFixtures extends WithDependenciesFixtures
{

    private ArticleRepository $articleRepository;
    private ArticleVersionRepository $articleVersionRepository;

    public function __construct(ArticleRepository $articleRepository, ArticleVersionRepository $articleVersionRepository)
    {
        $this->articleRepository = $articleRepository;
        $this->articleVersionRepository = $articleVersionRepository;
    }

    public function getDependencies(): array
    {
        return [ArticleFixtures::class];
    }

    public function load(ObjectManager $manager): void
    {
        $article = $this->articleRepository->find(6);
        $version = $this->articleVersionRepository->createNewVersionForArticle($article);
        $version->setSummary('Summary');
        $version->setContent('Content');
        $version->setActive(false);
        $version->setCommitMessage('Version added from ArticleVersion fixture.');
        $manager->persist($version);
        $manager->flush();
    }
}